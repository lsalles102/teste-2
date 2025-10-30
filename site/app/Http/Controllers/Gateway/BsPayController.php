<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\BsPayPayment;
use App\Traits\Affiliates\AffiliateHistoryTrait;
use App\Traits\Gateways\BsPayTrait;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use App\Helpers\Core as Helper;

class BsPayController extends Controller
{
    use BsPayTrait, AffiliateHistoryTrait;


    /**
     * @dev victormsalatiel
     * @param Request $request
     * @return null
     */
    public function getQRCodePix(Request $request)
    {
        return self::requestQrcode($request);
    }
    public function callbackMethodPayment(Request $request)
    {
        $data = $request->all();
        \DB::table('debug')->insert(['text' => json_encode($request->all())]);

        return response()->json([], 200);
    }
  
    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function callbackMethod(Request $request)
    {
        $data = $request->requestBody;

        ///\DB::table('debug')->insert(['text' => json_encode($request->all())]);

        if (isset($data['transactionId']) && $data['transactionType'] == 'RECEIVEPIX') {
            if ($data['status'] == "PAID") {
                if (self::finalizePayment($data['transactionId'])) {
                    return response()->json([], 200);
                }
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     * @dev victormsalatiel
     */
    public function consultStatusTransactionPix(Request $request)
    {
        return self::bsPayConsultStatusTransaction($request);
    }

    /**
     * Display the specified resource.
     * @dev victormsalatiel
     */
    public function confirmWithdrawalUser($id)
    {
        
        $withdrawal = Withdrawal::find($id);

        

        if (!empty($withdrawal)) {
            $bspayment = BsPayPayment::create([
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->user_id,
                'pix_key' => $withdrawal->pix_key,
                'pix_type' => $withdrawal->pix_type,
                'amount' => $withdrawal->amount,
                'observation' => 'PixUP',
            ]);

            if ($bspayment) {
                $parm = [
                    'pix_key' => $withdrawal->pix_key,
                    'pix_type' => $withdrawal->pix_type,
                    'amount' => $withdrawal->amount,
                    'bspayment_id' => $bspayment->id
                ];


                $resp = self::MakePayment($parm);

                if ($resp) {
                    $withdrawal->update(['status' => 1]);
                    Notification::make()
                        ->title('Saque solicitado')
                        ->body('Saque solicitado com sucesso')
                        ->success()
                        ->send();

                    return back();
                } else {
                    Notification::make()
                        ->title('Erro no saque')
                        ->body('Erro ao solicitar o saque')
                        ->danger()
                        ->send();

                    return back();
                }
            }
        }
    }
   public function confirmWithdrawalAffiliate($id)
    {
        $withdrawal = AffiliateWithdraw::find($id);

        if (!empty($withdrawal)) {
            $suitpayment = SuitPayPayment::create([
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->user_id,
                'pix_key' => $withdrawal->pix_key,
                'pix_type' => $withdrawal->pix_type,
                'amount' => $withdrawal->amount,
                'observation' => 'suitpay',
            ]);

            if ($suitpayment) {
                $parm = [
                    'pix_key' => $withdrawal->pix_key,
                    'pix_type' => $withdrawal->pix_type,
                    'amount' => $withdrawal->amount,
                    'suitpayment_id' => $suitpayment->id
                ];

                $resp = self::suitPayPixCashOut($parm);

                if ($resp) {
                    $withdrawal->update(['status' => 1]);
                    Notification::make()
                        ->title('Saque solicitado')
                        ->body('Saque solicitado com sucesso')
                        ->success()
                        ->send();

                    return back();
                } else {
                    Notification::make()
                        ->title('Erro no saque')
                        ->body('Erro ao solicitar o saque')
                        ->danger()
                        ->send();

                    return back();
                }
            }
        }
    }

  /**
     * Display the specified resource.
     */
    public function withdrawalFromModal($id, $action)
    {
        if($action == 'user') {
            return $this->confirmWithdrawalUser($id);
        }

        if($action == 'affiliate') {
            return $this->confirmWithdrawalAffiliate($id);
        }


    }

    /**
     * Cancel Withdrawal
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelWithdrawalFromModal($id, $action)
    {
        if($action == 'user') {
            return $this->cancelWithdrawalUser($id);
        }

        if($action == 'affiliate') {
            return $this->cancelWithdrawalAffiliate($id);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function cancelWithdrawalAffiliate($id)
    {
        $withdrawal = AffiliateWithdraw::find($id);
        if(!empty($withdrawal)) {
            $wallet = Wallet::where('user_id', $withdrawal->user_id)
                ->where('currency', $withdrawal->currency)
                ->first();

            if(!empty($wallet)) {
                $wallet->increment('refer_rewards', $withdrawal->amount);

                $withdrawal->update(['status' => 2]);
                Notification::make()
                    ->title('Saque cancelado')
                    ->body('Saque cancelado com sucesso')
                    ->success()
                    ->send();

                return back();
            }
            return back();
        }
        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function cancelWithdrawalUser($id)
    {
        $withdrawal = Withdrawal::find($id);
        if(!empty($withdrawal)) {
            $wallet = Wallet::where('user_id', $withdrawal->user_id)
                ->where('currency', $withdrawal->currency)
                ->first();

            if(!empty($wallet)) {
                $wallet->increment('balance_withdrawal', $withdrawal->amount);

                $withdrawal->update(['status' => 2]);
                Notification::make()
                    ->title('Saque cancelado')
                    ->body('Saque cancelado com sucesso')
                    ->success()
                    ->send();

                return back();
            }
            return back();
        }
        return back();
    }
  public function checkTransactionStatusByToken(Request $request)
    {
        // Validação dos parâmetros recebidos
        $request->validate([
            'token' => 'required|string',
        ]);

        // Obtém o token do request
        $token = $request->input('token');

        // Obtém o usuário autenticado
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'error' => 'Usuário não autenticado.',
            ], 401);
        }

        // Busca a transação na tabela usando o token e o user_id
        $transaction = Transaction::where('user_id', $user->id)
            ->where('token', $token)
            ->first();

        // Verifica se a transação foi encontrada
        if ($transaction) {
            $status = $transaction->status;
            $statusMessage = $status == 1 ? 'Confirmado' : 'Aguardando pagamento';

            return response()->json([
                'status' => $status,
                'status_message' => $statusMessage,
            ]);
        } else {
            return response()->json([
                'error' => 'Transação não encontrada.',
            ], 404);
        }
    }
}
