INSERT INTO `User` (`firstName`, `lastName`, `email`, `gender`, `password`, `last_login`, `admin`, `activePlayer`, `reporter`, `playerId`, `bday`, `phone`, `image`, `dsgvo`, `dsgvo_timestamp`)
VALUES ('Admin', 'admin', 'admin@admin.com', 1, 'd033e22ae348aeb5660fc2140aec35850c4da997', now(), '1', '1', '0', NULL, NULL, '', '', '0', now());


INSERT INTO `UserPassHash` (`userId`, `token`, `ip`, `createDate`, `valid`)
VALUES ('1', 'ca3cfb7bf899f5f9a85b30a085d3688f9c34eb97f00e8d90cc20d52cf4d8e03c', '', now(), '1');