-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 26/04/2024 às 08:10
-- Versão do servidor: 8.0.24
-- Versão do PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `tigre`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agents`
--

CREATE TABLE `agents` (
  `id` int NOT NULL,
  `agentCode` varchar(50) DEFAULT NULL,
  `senha` text NOT NULL,
  `saldo` float NOT NULL DEFAULT '0',
  `agentToken` varchar(255) NOT NULL,
  `secretKey` varchar(255) NOT NULL,
  `probganho` varchar(50) DEFAULT '0',
  `probbonus` varchar(10) DEFAULT '0',
  `probganhortp` varchar(10) DEFAULT '0',
  `probganhoinfluencer` varchar(10) DEFAULT '0',
  `probbonusinfluencer` varchar(10) DEFAULT '0',
  `probganhoaposta` varchar(10) DEFAULT '0',
  `probganhosaldo` varchar(10) DEFAULT '0',
  `callbackurl` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `agents`
--

INSERT INTO `agents` (`id`, `agentCode`, `senha`, `saldo`, `agentToken`, `secretKey`, `probganho`, `probbonus`, `probganhortp`, `probganhoinfluencer`, `probbonusinfluencer`, `probganhoaposta`, `probganhosaldo`, `callbackurl`) VALUES
(1, 'brabobet', '123321', 1000, '40c18270-f18e-42c4-90a5-7f77b8f68e6b', '9561fdd1-7158-4ead-bcd2-2d2b53be2ccb', '050', '030', '030', '009', '100', '50', '030', 'https://brabobet.online/');

-- --------------------------------------------------------

--
-- Estrutura para tabela `bikineparadisejson`
--

CREATE TABLE `bikineparadisejson` (
  `id` int NOT NULL,
  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT (_utf8mb4'{"dt":{"si":{"wp":null,"lw":null,"orl":null,"wm":0,"rwm":null,"wabm":0.0,"fs":null,"sc":0,"wppr":[[],[],[0,1,2,3],[],[]],"gwt":0,"fb":null,"ctw":0.0,"pmt":null,"cwc":0,"fstc":null,"pcwc":0,"rwsp":null,"hashr":null,"ml":2,"cs":0.3,"rl":[3,8,4,12,9,1,10,5,0,0,0,0,9,1,10,5,3,8,4,12],"sid":"0","psid":"0","st":1,"nst":1,"pf":0,"aw":0.00,"wid":0,"wt":"C","wk":"0_C","wbn":null,"wfg":null,"blb":0.00,"blab":0.00,"bl":100000.00,"tb":0.00,"tbb":0.00,"tw":0.00,"np":0.00,"ocr":null,"mr":null,"ge":null}},"err":null}')
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `bikineparadisejson`
--

INSERT INTO `bikineparadisejson` (`id`, `json`) VALUES
(13, '{\"dt\":{\"si\":{\"wp\":{\"9\":[2,5,10],\"12\":[1,5,10],\"22\":[0,5,10]},\"lw\":{\"9\":3.5999999999999996,\"12\":5.3999999999999995,\"22\":3.5999999999999996},\"orl\":null,\"wm\":0,\"rwm\":null,\"wabm\":12.6,\"fs\":null,\"sc\":0,\"wppr\":[[],[0,1],[2,3],[],[]],\"gwt\":-1,\"fb\":null,\"ctw\":12.6,\"pmt\":null,\"cwc\":1,\"fstc\":null,\"pcwc\":1,\"rwsp\":{\"9\":10,\"12\":15,\"22\":10},\"hashr\":\"0:0;11;11;12;7#0;12;7;11;6#5;8;8;5;9#10;10;5;4;1#R#11#001020#MV#15.0#MT#1#R#8#011222#MV#15.0#MT#1#R#11#011020#MV#15.0#MT#1#MG#9.0#\",\"ml\":\"3\",\"cs\":\"0.12\",\"rl\":[7,4,6,10,0,0,12,11,5,12,0,0,12,11,4,10,10,9,7,6],\"sid\":\"1767263948430573056\",\"psid\":\"1767263948430573056\",\"st\":1,\"nst\":1,\"pf\":1,\"aw\":12.6,\"wid\":0,\"wt\":\"C\",\"wk\":\"0_C\",\"wbn\":null,\"wfg\":null,\"blb\":2181.59,\"blab\":2185.19,\"bl\":2185.19,\"tb\":9,\"tbb\":9,\"tw\":12.6,\"np\":-9,\"ocr\":null,\"mr\":null,\"ge\":[1,11]}},\"err\":null}');

-- --------------------------------------------------------

--
-- Estrutura para tabela `butterflyblossomplayerjson`
--

CREATE TABLE `butterflyblossomplayerjson` (
  `id` int NOT NULL,
  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT (_utf8mb4'{"dt":{"si":{"wp":null,"wp3x5":null,"wpl":null,"ptbr":null,"lw":null,"lwm":null,"rl3x5":[5,6,7,2,0,8,3,1,4,2,0,8,5,6,7],"swl":[[6,3],[8,2],[14,1]],"swlb":[[6,3],[8,2],[14,1]],"nswl":null,"rswl":null,"rs":null,"fs":null,"sc":0,"saw":0.0,"tlw":0.0,"gm":1,"gmi":0,"gml":[1,2,3,5],"gwt":0,"fb":null,"ctw":0.0,"pmt":null,"cwc":0,"fstc":null,"pcwc":0,"rwsp":null,"hashr":null,"ml":2,"cs":0.3,"rl":[1,5,6,7,4,2,0,8,0,3,1,4,4,2,0,8,1,5,6,7],"sid":"0","psid":"0","st":1,"nst":1,"pf":0,"aw":0.00,"wid":0,"wt":"C","wk":"0_C","wbn":null,"wfg":null,"blb":0.00,"blab":0.00,"bl":100000.00,"tb":0.00,"tbb":0.00,"tw":0.00,"np":0.00,"ocr":null,"mr":null,"ge":null}},"err":null}')
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `calls`
--

CREATE TABLE `calls` (
  `id` int NOT NULL,
  `iduser` int NOT NULL,
  `gamecode` varchar(255) NOT NULL,
  `jsonname` varchar(255) NOT NULL DEFAULT '0',
  `steps` int DEFAULT NULL,
  `bycall` varchar(255) DEFAULT NULL,
  `aw` float DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `calls`
--

INSERT INTO `calls` (`id`, `iduser`, `gamecode`, `jsonname`, `steps`, `bycall`, `aw`, `status`) VALUES
(2435, 14, 'fortune-tiger', '8', 0, 'system', 0, 'completed'),
(2436, 14, 'fortune-tiger', '12', 0, 'system', 0, 'completed'),
(2437, 13, 'fortune-dragon', '10', 0, 'system', 1.12, 'completed'),
(2438, 13, 'fortune-dragon', '12', 0, 'system', 4.48, 'completed'),
(2439, 13, 'fortune-dragon', '9', 0, 'system', 9.6, 'completed'),
(2440, 13, 'fortune-dragon', '7', 0, 'system', 135.36, 'completed'),
(2441, 13, 'fortune-dragon', '11', 0, 'system', 22.08, 'completed'),
(2442, 13, 'fortune-dragon', '1', 0, 'system', 48, 'completed'),
(2443, 13, 'fortune-dragon', '7', 0, 'system', 90.24, 'completed'),
(2444, 13, 'fortune-rabbit', '10', 0, 'system', 6.2, 'completed'),
(2445, 13, 'fortune-rabbit', '7', 0, 'system', 630, 'completed'),
(2446, 13, 'fortune-rabbit', '8', 0, 'system', 5875, 'completed'),
(2447, 13, 'fortune-rabbit', '12', 0, 'system', 7000, 'completed'),
(2448, 13, 'jungle-delight', '11', 0, 'system', 0.16, 'completed'),
(2449, 13, 'dragon-tiger-luck', '8', NULL, 'system', 0, 'completed'),
(2450, 13, 'ganesha-gold', '8', 0, 'system', 3.88, 'completed'),
(2451, 13, 'double-fortune', '9', 0, 'system', 150, 'completed'),
(2452, 13, 'bikini-paradise', '11', 0, 'system', 95.4, 'completed'),
(2453, 13, 'fortune-dragon', '7', 0, 'system', 45.12, 'completed'),
(2454, 13, 'fortune-dragon', '7', 0, 'system', 45.12, 'completed');

-- --------------------------------------------------------

--
-- Estrutura para tabela `doublefortunejson`
--

CREATE TABLE `doublefortunejson` (
  `id` int NOT NULL,
  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT (_utf8mb4'{"dt":{"si":{"wp":null,"lw":null,"lwm":null,"slw":null,"nk":null,"sc":0,"fs":null,"gwt":0,"fb":null,"ctw":0.0,"pmt":null,"cwc":0,"fstc":null,"pcwc":0,"rwsp":null,"hashr":null,"ml":1,"cs":0.01,"rl":[8,16,9,11,5,18,1,2,4,12,6,17,7,15,10],"sid":"0","psid":"0","st":1,"nst":1,"pf":0,"aw":0.00,"wid":0,"wt":"C","wk":"0_C","wbn":null,"wfg":null,"blb":0.00,"blab":0.00,"bl":0.31,"tb":0.00,"tbb":0.00,"tw":0.00,"np":0.00,"ocr":null,"mr":null,"ge":null}}}')
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `doublefortunejson`
--

INSERT INTO `doublefortunejson` (`id`, `json`) VALUES
(13, '{\"dt\":{\"si\":{\"wp\":{\"2\":[0,3],\"6\":[0,3,7],\"9\":[1,3],\"15\":[2,3],\"16\":[1,3,7],\"19\":[2,3],\"23\":[2,3,7],\"24\":[1,3],\"26\":[0,3],\"28\":[0,3]},\"lw\":{\"2\":20,\"6\":32,\"9\":20,\"15\":20,\"16\":32,\"19\":20,\"23\":32,\"24\":20,\"26\":20,\"28\":20},\"lwm\":null,\"slw\":[236],\"nk\":{\"2\":3,\"6\":4,\"9\":3,\"15\":3,\"16\":4,\"19\":3,\"23\":4,\"24\":3,\"26\":3,\"28\":3},\"sc\":1,\"fs\":null,\"gwt\":-1,\"fb\":null,\"ctw\":27,\"pmt\":null,\"cwc\":1,\"fstc\":null,\"pcwc\":1,\"rwsp\":{\"0\":{\"2\":5,\"6\":8,\"9\":5,\"15\":5,\"16\":8,\"19\":5,\"23\":8,\"24\":5,\"26\":5,\"28\":5}},\"hashr\":\"0:11;10;15;5;17#13;2;12;13;14#10;17;17;3;1#R#10#0210#MV#18.0#MT#1#R#10#0210#MV#18.0#MT#1#R#10#0210#MV#18.0#MT#1#MG#27.0#\",\"ml\":\"10\",\"cs\":\"0.4\",\"rl\":[15,15,15,1,8,2,7,15,5,3,5,9,11,3,9],\"sid\":\"1772672877272694272\",\"psid\":\"1772672877272694272\",\"st\":1,\"nst\":1,\"pf\":1,\"aw\":236,\"wid\":0,\"wt\":\"C\",\"wk\":\"0_C\",\"wbn\":null,\"wfg\":null,\"blb\":2050.09,\"blab\":2166.09,\"bl\":2166.09,\"tb\":120,\"tbb\":120,\"tw\":236,\"np\":236,\"ocr\":null,\"mr\":null,\"ge\":[1,11]}},\"err\":null}');

-- --------------------------------------------------------

--
-- Estrutura para tabela `dragontigerluckjson`
--

CREATE TABLE `dragontigerluckjson` (
  `id` int NOT NULL,
  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT (_utf8mb4'{"dt":{"si":{"mrl":{"1":{"wp":null,"lw":null,"tw":0.00,"rl":[1,2,3,2,3,1,2,0,3],"orl":[2,3,0]},"2":{"wp":null,"lw":null,"tw":0.00,"rl":[2,0,1,3,1,2,3,2,1],"orl":[0,1,2]}},"gpt":3,"gwt":0,"fb":null,"ctw":0.0,"pmt":null,"cwc":0,"fstc":null,"pcwc":0,"rwsp":null,"hashr":null,"ml":1,"cs":0.5,"rl":null,"sid":"0","psid":"0","st":1,"nst":1,"pf":0,"aw":0.00,"wid":0,"wt":"C","wk":"0_C","wbn":null,"wfg":null,"blb":0.00,"blab":0.00,"bl":0.26,"tb":0.00,"tbb":0.00,"tw":0.00,"np":0.00,"ocr":null,"mr":null,"ge":null}},"err":null}')
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `dragontigerluckjson`
--

INSERT INTO `dragontigerluckjson` (`id`, `json`) VALUES
(13, '{\"dt\":{\"si\":{\"mrl\":{\"1\":{\"wp\":null,\"lw\":null,\"tw\":0,\"rl\":[1,0,3,3,2,3,3,0,1],\"orl\":[0,2,0]},\"2\":{\"wp\":null,\"lw\":null,\"tw\":0,\"rl\":[1,2,3,1,2,3,1,0,1],\"orl\":[2,2,0]}},\"gpt\":3,\"gwt\":-1,\"fb\":null,\"ctw\":0,\"pmt\":null,\"cwc\":0,\"fstc\":null,\"pcwc\":0,\"rwsp\":null,\"hashr\":\"0:3;0;1#MV#2.50#MT#1#MG#0#\",\"ml\":\"1\",\"cs\":\"0.5\",\"rl\":[-1,-1,-1],\"sid\":\"1773496005090742272\",\"psid\":\"1773496005090742272\",\"st\":1,\"nst\":1,\"pf\":1,\"aw\":0,\"wid\":0,\"wt\":\"C\",\"wk\":\"0_C\",\"wbn\":null,\"wfg\":null,\"blb\":2246.03,\"blab\":2245.03,\"bl\":2245.03,\"tb\":1,\"tbb\":1,\"tw\":0,\"np\":-1,\"ocr\":null,\"mr\":null,\"ge\":[1,11]}},\"err\":null}');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fortunedragonplayerjson`
--

CREATE TABLE `fortunedragonplayerjson` (
  `id` int NOT NULL,
  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT (_utf8mb4'{"dt":{"si":{"wp":null,"lw":null,"gm":1,"it":false,"orl":[2,2,5,0,0,0,6,3,3],"fs":null,"mf":{"mt":[2],"ms":[true],"mi":[0]},"ssaw":0.00,"crtw":0.0,"imw":false,"gwt":0,"fb":null,"ctw":0.0,"pmt":null,"cwc":0,"fstc":null,"pcwc":0,"rwsp":null,"hashr":null,"ml":2,"cs":0.3,"rl":[2,2,5,0,0,0,6,3,3],"sid":"0","psid":"0","st":1,"nst":1,"pf":0,"aw":0.00,"wid":0,"wt":"C","wk":"0_C","wbn":null,"wfg":null,"blb":0.00,"blab":0.00,"bl":100000.00,"tb":0.00,"tbb":0.00,"tw":0.00,"np":0.00,"ocr":null,"mr":null,"ge":null}},"err":null}')
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `fortunedragonplayerjson`
--

INSERT INTO `fortunedragonplayerjson` (`id`, `json`) VALUES
(13, '{\"dt\":{\"si\":{\"wp\":{\"2\":[0,3,6],\"4\":[0,4,8]},\"lw\":{\"2\":0.24,\"4\":0.24},\"gm\":7,\"it\":false,\"orl\":[6,2,2,6,6,5,6,6,6],\"fs\":{\"s\":0,\"ts\":8,\"aw\":45.12},\"mf\":{\"mt\":[10,2,2,10],\"ms\":[false,true,true,false],\"mi\":[1,2]},\"ssaw\":1.92,\"crtw\":0,\"imw\":false,\"gwt\":-1,\"fb\":null,\"ctw\":1.92,\"pmt\":null,\"cwc\":1,\"fstc\":null,\"pcwc\":0,\"rwsp\":null,\"hashr\":\"0:6;5;2#6;5;2#5;7;5#MV#3.0#MT#7#MG#0#\",\"ml\":\"1\",\"cs\":\"0.08\",\"rl\":[6,2,2,6,6,5,6,6,6],\"sid\":\"1762619032324734464\",\"psid\":\"1762619032324734464\",\"st\":2,\"nst\":1,\"pf\":1,\"aw\":45.12,\"wid\":0,\"wt\":\"C\",\"wk\":\"0_C\",\"wbn\":null,\"wfg\":null,\"blb\":2248.71,\"blab\":2250.23,\"bl\":2250.23,\"tb\":0.4,\"tbb\":0.4,\"tw\":1.92,\"np\":-0.4,\"ocr\":null,\"mr\":null,\"ge\":[2,11]}},\"err\":null}'),
(15, '{\"dt\":{\"si\":{\"wp\":null,\"lw\":null,\"gm\":1,\"it\":false,\"orl\":[4,4,5,7,7,0,7,4,6],\"fs\":null,\"mf\":{\"mt\":[2,5],\"ms\":[false,false],\"mi\":[]},\"ssaw\":0,\"crtw\":0,\"imw\":false,\"gwt\":-1,\"fb\":null,\"ctw\":0,\"pmt\":null,\"cwc\":0,\"fstc\":null,\"pcwc\":0,\"rwsp\":null,\"hashr\":\"0:2;5;3#4;5;3#3;4;5#MV#3.0#MT#1#MG#0#\",\"ml\":\"1\",\"cs\":\"0.08\",\"rl\":[4,4,5,7,7,0,7,4,6],\"sid\":\"1762525206562143744\",\"psid\":\"1762525206562143744\",\"st\":1,\"nst\":1,\"pf\":1,\"aw\":0,\"wid\":0,\"wt\":\"C\",\"wk\":\"0_C\",\"wbn\":null,\"wfg\":null,\"blb\":252.44,\"blab\":252.04,\"bl\":252.04,\"tb\":0.4,\"tbb\":0.4,\"tw\":0,\"np\":-0.4,\"ocr\":null,\"mr\":null,\"ge\":[1,11]}},\"err\":null}');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fortunemouseplayerjson`
--

CREATE TABLE `fortunemouseplayerjson` (
  `id` int NOT NULL,
  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT (_utf8mb4'{"dt":{"si":{"wp":null,"lw":null,"orl":null,"idr":false,"ir":false,"ist":false,"rc":0,"itw":false,"wc":0,"gwt":0,"fb":null,"ctw":0.0,"pmt":null,"cwc":0,"fstc":null,"pcwc":0,"rwsp":null,"hashr":null,"ml":2,"cs":0.3,"rl":[1,1,1,0,0,0,2,2,2],"sid":"0","psid":"0","st":1,"nst":1,"pf":0,"aw":0.00,"wid":0,"wt":"C","wk":"0_C","wbn":null,"wfg":null,"blb":0.00,"blab":0.00,"bl":100000.00,"tb":0.00,"tbb":0.00,"tw":0.00,"np":0.00,"ocr":null,"mr":null,"ge":null}},"err":null}')
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fortuneoxrplayerjson`
--

CREATE TABLE `fortuneoxrplayerjson` (
  `id` int NOT NULL,
  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT (_utf8mb4'{"dt":{"si":{"wc":31,"ist":false,"itw":true,"fws":0,"wp":null,"orl":[5,7,6,5,6,3,3,7,6],"lw":null,"irs":false,"gwt":-1,"fb":null,"ctw":0,"pmt":null,"cwc":0,"fstc":null,"pcwc":0,"rwsp":null,"hashr":"0:2;5;4#3;3;6#7;3;6#MV#3.0#MT#1#MG#0#","ml":"1","cs":"0.08","rl":[5,7,6,5,6,3,3,7,6],"sid":"1758600495495052800","psid":"1758600495495052800","st":1,"nst":1,"pf":1,"aw":0,"wid":0,"wt":"C","wk":"0_C","wbn":null,"wfg":null,"blb":44409,"blab":44408.6,"bl":44408.6,"tb":0.4,"tbb":0.4,"tw":0,"np":-0.4,"ocr":null,"mr":null,"ge":[1,11]}},"err":null}')
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fortunerabbitplayerjson`
--

CREATE TABLE `fortunerabbitplayerjson` (
  `id` int NOT NULL,
  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT (_utf8mb4'{"dt":{"si":{"wp":null,"lw":null,"orl":[2,2,0,99,8,8,8,8,2,2,0,99],"ift":false,"iff":false,"cpf":{"1":{"p":4,"bv":3000.00,"m":500.0},"2":{"p":5,"bv":120.00,"m":20.0},"3":{"p":6,"bv":30.00,"m":5.0},"4":{"p":7,"bv":3.00,"m":0.5}},"cptw":0.0,"crtw":0.0,"imw":false,"fs":null,"gwt":0,"fb":null,"ctw":0.0,"pmt":null,"cwc":0,"fstc":null,"pcwc":0,"rwsp":null,"hashr":null,"ml":2,"cs":0.3,"rl":[2,2,0,99,8,8,8,8,2,2,0,99],"sid":"0","psid":"0","st":1,"nst":1,"pf":0,"aw":0.00,"wid":0,"wt":"C","wk":"0_C","wbn":null,"wfg":null,"blb":0.00,"blab":0.00,"bl":100000.00,"tb":0.00,"tbb":0.00,"tw":0.00,"np":0.00,"ocr":null,"mr":null,"ge":null},"cc":"PGC"},"err":null}')
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `fortunerabbitplayerjson`
--

INSERT INTO `fortunerabbitplayerjson` (`id`, `json`) VALUES
(13, '{\"dt\":{\"si\":{\"wp\":null,\"lw\":null,\"orl\":[2,7,7,99,7,6,7,6,3,3,3,99],\"ift\":false,\"iff\":false,\"cpf\":{},\"cptw\":0,\"crtw\":0,\"imw\":false,\"fs\":null,\"gwt\":-1,\"fb\":null,\"ctw\":0,\"pmt\":null,\"cwc\":0,\"fstc\":null,\"pcwc\":0,\"rwsp\":null,\"hashr\":\"0:6;7;6#6;7;3#4;6;3#99;6;99#MV#6.0#MT#1#MG#0#\",\"ml\":\"10\",\"cs\":\"10\",\"rl\":[2,7,7,99,7,6,7,6,3,3,3,99],\"sid\":\"1762943147845811712\",\"psid\":\"1762943147845811712\",\"st\":1,\"nst\":1,\"pf\":1,\"aw\":0,\"wid\":0,\"wt\":\"C\",\"wk\":\"0_C\",\"wbn\":null,\"wfg\":null,\"blb\":16547.4,\"blab\":16047.400000000001,\"bl\":16047.400000000001,\"tb\":500,\"tbb\":500,\"tw\":0,\"np\":-500,\"ocr\":null,\"mr\":null,\"ge\":[1,11]}},\"err\":null}');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fortunetigerplayerjson`
--

CREATE TABLE `fortunetigerplayerjson` (
  `id` int NOT NULL,
  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT (_utf8mb4'{"dt":{"si":{"wc":31,"ist":false,"itw":true,"fws":0,"wp":null,"orl":[5,7,6,5,6,3,3,7,6],"lw":null,"irs":false,"gwt":-1,"fb":null,"ctw":0,"pmt":null,"cwc":0,"fstc":null,"pcwc":0,"rwsp":null,"hashr":"0:2;5;4#3;3;6#7;3;6#MV#3.0#MT#1#MG#0#","ml":"1","cs":"0.08","rl":[5,7,6,5,6,3,3,7,6],"sid":"1758600495495052800","psid":"1758600495495052800","st":1,"nst":1,"pf":1,"aw":0,"wid":0,"wt":"C","wk":"0_C","wbn":null,"wfg":null,"blb":44409,"blab":44408.6,"bl":44408.6,"tb":0.4,"tbb":0.4,"tw":0,"np":-0.4,"ocr":null,"mr":null,"ge":[1,11]}},"err":null}')
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `fortunetigerplayerjson`
--

INSERT INTO `fortunetigerplayerjson` (`id`, `json`) VALUES
(13, '{\"dt\":{\"si\":{\"wc\":31,\"ist\":true,\"itw\":true,\"fws\":0,\"wp\":null,\"orl\":[6,6,6,5,3,6,6,7,5],\"lw\":null,\"irs\":false,\"gwt\":-1,\"fb\":null,\"ctw\":0,\"pmt\":null,\"cwc\":0,\"fstc\":null,\"pcwc\":0,\"rwsp\":null,\"hashr\":\"0:2;5;4#3;3;6#7;3;6#MV#3.0#MT#1#MG#0#\",\"ml\":\"1\",\"cs\":\"0.08\",\"rl\":[6,6,6,5,3,6,6,7,5],\"sid\":\"1758600495495052800\",\"psid\":\"1758600495495052800\",\"st\":1,\"nst\":1,\"pf\":1,\"aw\":0,\"wid\":0,\"wt\":\"C\",\"wk\":\"0_C\",\"wbn\":null,\"wfg\":null,\"blb\":2247.43,\"blab\":2247.0299999999997,\"bl\":2247.0299999999997,\"tb\":0.4,\"tbb\":0.4,\"tw\":0,\"np\":-0.4,\"ocr\":null,\"mr\":null,\"ge\":[1,11]}},\"err\":null}'),
(14, '{\"dt\":{\"si\":{\"wc\":31,\"ist\":false,\"itw\":true,\"fws\":0,\"wp\":null,\"orl\":[7,7,7,6,6,6,6,7,7],\"lw\":null,\"irs\":false,\"gwt\":-1,\"fb\":null,\"ctw\":0,\"pmt\":null,\"cwc\":0,\"fstc\":null,\"pcwc\":0,\"rwsp\":null,\"hashr\":\"0:2;5;4#3;3;6#7;3;6#MV#3.0#MT#1#MG#0#\",\"ml\":\"0.08\",\"cs\":\"5\",\"rl\":[7,7,7,6,6,6,6,7,7],\"sid\":\"1758600495495052800\",\"psid\":\"1758600495495052800\",\"st\":1,\"nst\":1,\"pf\":1,\"aw\":0,\"wid\":0,\"wt\":\"C\",\"wk\":\"0_C\",\"wbn\":null,\"wfg\":null,\"blb\":114.48,\"blab\":112.48,\"bl\":112.48,\"tb\":2,\"tbb\":2,\"tw\":0,\"np\":-2,\"ocr\":null,\"mr\":null,\"ge\":[1,11]}},\"err\":null}'),
(16, '{\"dt\":{\"si\":{\"wc\":31,\"ist\":false,\"itw\":true,\"fws\":0,\"wp\":null,\"orl\":[5,2,4,7,7,7,2,6,5],\"lw\":null,\"irs\":false,\"gwt\":-1,\"fb\":null,\"ctw\":0,\"pmt\":null,\"cwc\":0,\"fstc\":null,\"pcwc\":0,\"rwsp\":null,\"hashr\":\"0:2;5;4#3;3;6#7;3;6#MV#3.0#MT#1#MG#0#\",\"ml\":\"1\",\"cs\":\"0.08\",\"rl\":[5,2,4,7,7,7,2,6,5],\"sid\":\"1758600495495052800\",\"psid\":\"1758600495495052800\",\"st\":1,\"nst\":1,\"pf\":1,\"aw\":0,\"wid\":0,\"wt\":\"C\",\"wk\":\"0_C\",\"wbn\":null,\"wfg\":null,\"blb\":0.45,\"blab\":0.04999999999999999,\"bl\":0.04999999999999999,\"tb\":0.4,\"tbb\":0.4,\"tw\":0,\"np\":-0.4,\"ocr\":null,\"mr\":null,\"ge\":[1,11]}},\"err\":null}');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ganeshagoldjson`
--

CREATE TABLE `ganeshagoldjson` (
  `id` int NOT NULL,
  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT (_utf8mb4'{"dt":{"si":{"wp":null,"lw":null,"ltw":0.0,"snww":null,"fs":null,"sc":0,"gwt":0,"fb":null,"ctw":0.0,"pmt":null,"cwc":0,"fstc":null,"pcwc":0,"rwsp":null,"hashr":null,"ml":2,"cs":0.3,"rl":[2,1,5,4,3,3,0,9,7,8,8,6,7,3,6],"sid":"0","psid":"0","st":1,"nst":1,"pf":0,"aw":0.00,"wid":0,"wt":"C","wk":"0_C","wbn":null,"wfg":null,"blb":0.00,"blab":0.00,"bl":100000.00,"tb":0.00,"tbb":0.00,"tw":0.00,"np":0.00,"ocr":null,"mr":null,"ge":null}},"err":null}')
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `ganeshagoldjson`
--

INSERT INTO `ganeshagoldjson` (`id`, `json`) VALUES
(13, '{\"dt\":{\"si\":{\"wp\":null,\"lw\":null,\"ltw\":0,\"snww\":null,\"fs\":null,\"sc\":1,\"gwt\":-1,\"fb\":null,\"ctw\":0,\"pmt\":null,\"cwc\":0,\"fstc\":null,\"pcwc\":0,\"rwsp\":null,\"hashr\":\"0:6;8;3;5;6#7;8;9;6;1#2;6;2;7;5#MV#9.00#MT#1#MG#0#\",\"ml\":\"1\",\"cs\":\"0.01\",\"rl\":[5,9,3,6,9,1,8,7,6,9,3,8,4,6,5],\"sid\":\"1773390084810145280\",\"psid\":\"1773390084810145280\",\"st\":1,\"nst\":1,\"pf\":1,\"aw\":0,\"wid\":0,\"wt\":\"C\",\"wk\":\"0_C\",\"wbn\":null,\"wfg\":null,\"blb\":624.59,\"blab\":624.2900000000001,\"bl\":624.2900000000001,\"tb\":0.3,\"tbb\":0.3,\"tw\":0,\"np\":-0.3,\"ocr\":null,\"mr\":null,\"ge\":[1,11]}},\"err\":null}');

-- --------------------------------------------------------

--
-- Estrutura para tabela `jungledelightjson`
--

CREATE TABLE `jungledelightjson` (
  `id` int NOT NULL,
  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT (_utf8mb4'{"dt":{"si":{"wp":null,"lw":null,"c":null,"orl":null,"fs":null,"gwt":0,"fb":null,"ctw":0.0,"pmt":null,"cwc":0,"fstc":null,"pcwc":0,"rwsp":null,"hashr":null,"ml":1,"cs":0.02,"rl":[3,6,7,6,3,7,4,5,4,8,9,7,9,8,7],"sid":"0","psid":"0","st":1,"nst":1,"pf":0,"aw":0.00,"wid":0,"wt":"C","wk":"0_C","wbn":null,"wfg":null,"blb":0.00,"blab":0.00,"bl":0.62,"tb":0.00,"tbb":0.00,"tw":0.00,"np":0.00,"ocr":null,"mr":null,"ge":null}},"err":null}')
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `jungledelightjson`
--

INSERT INTO `jungledelightjson` (`id`, `json`) VALUES
(13, '{\"dt\":{\"si\":{\"wp\":null,\"lw\":null,\"c\":null,\"orl\":null,\"fs\":null,\"gwt\":-1,\"fb\":null,\"ctw\":0,\"pmt\":null,\"cwc\":0,\"fstc\":null,\"pcwc\":0,\"rwsp\":null,\"hashr\":\"0:7;8;8;6;4#5;1;8;6;3#5;9;8;6;9#MV#0.60#MT#1#MG#0#\",\"ml\":\"1\",\"cs\":\"0.02\",\"rl\":[4,4,5,5,6,9,1,6,9,1,8,6,8,8,8],\"sid\":\"1771284745113501184\",\"psid\":\"1771284745113501184\",\"st\":1,\"nst\":1,\"pf\":1,\"aw\":0,\"wid\":0,\"wt\":\"C\",\"wk\":\"0_C\",\"wbn\":null,\"wfg\":null,\"blb\":2248.23,\"blab\":2247.83,\"bl\":2247.83,\"tb\":0.4,\"tbb\":0.4,\"tw\":0,\"np\":-0.4,\"ocr\":null,\"mr\":null,\"ge\":[1,11]}},\"err\":null}');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  `atk` varchar(255) NOT NULL,
  `saldo` float NOT NULL DEFAULT '0',
  `valorapostado` float NOT NULL DEFAULT '0',
  `valordebitado` float NOT NULL DEFAULT '0',
  `valorganho` float NOT NULL DEFAULT '0',
  `rtp` double NOT NULL DEFAULT '0',
  `isinfluencer` float NOT NULL DEFAULT '0',
  `agentid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `username`, `token`, `atk`, `saldo`, `valorapostado`, `valordebitado`, `valorganho`, `rtp`, `isinfluencer`, `agentid`) VALUES
(13, '83', '524a82e3-3eba-4c1c-a7d5-b88f673c9789', 'e0663049-d6a3-45df-9707-97230c819b9b', 2245.03, 20357.7, 20357.7, 30997.6, 152, 0, 1),
(14, '112', '66cfd33a-af62-4521-b0df-a53d34e92339', '78650307-f1cc-4e4d-a60b-f55dfec7cd48', 112.48, 72.4, 72.4, 84.88, 117, 0, 1),
(15, '1', '263fd255-fc34-4116-a58c-c48d5cf41470', '6952cd17-f520-4d4c-9d3f-eb819cbe02fd', 252.04, 3.6, 3.6, 0, 0, 0, 1),
(16, '116', '5288e4c9-5a55-4494-90c0-16d88f3b1984', 'd5919f33-ce85-49a3-8ac9-543c02fe8ece', 0.05, 1.2, 1.2, 0, 0, 0, 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Índices de tabela `bikineparadisejson`
--
ALTER TABLE `bikineparadisejson`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `id` (`id`) USING BTREE;

--
-- Índices de tabela `butterflyblossomplayerjson`
--
ALTER TABLE `butterflyblossomplayerjson`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `id` (`id`) USING BTREE;

--
-- Índices de tabela `calls`
--
ALTER TABLE `calls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Índices de tabela `doublefortunejson`
--
ALTER TABLE `doublefortunejson`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `id` (`id`) USING BTREE;

--
-- Índices de tabela `dragontigerluckjson`
--
ALTER TABLE `dragontigerluckjson`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `id` (`id`) USING BTREE;

--
-- Índices de tabela `fortunedragonplayerjson`
--
ALTER TABLE `fortunedragonplayerjson`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `id` (`id`) USING BTREE;

--
-- Índices de tabela `fortunemouseplayerjson`
--
ALTER TABLE `fortunemouseplayerjson`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `id` (`id`) USING BTREE;

--
-- Índices de tabela `fortuneoxrplayerjson`
--
ALTER TABLE `fortuneoxrplayerjson`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `id` (`id`) USING BTREE;

--
-- Índices de tabela `fortunerabbitplayerjson`
--
ALTER TABLE `fortunerabbitplayerjson`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `id` (`id`) USING BTREE;

--
-- Índices de tabela `fortunetigerplayerjson`
--
ALTER TABLE `fortunetigerplayerjson`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Índices de tabela `ganeshagoldjson`
--
ALTER TABLE `ganeshagoldjson`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `id` (`id`) USING BTREE;

--
-- Índices de tabela `jungledelightjson`
--
ALTER TABLE `jungledelightjson`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `id` (`id`) USING BTREE;

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `calls`
--
ALTER TABLE `calls`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2455;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
