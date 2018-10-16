<?php

define("CONTROLLER_PATH",'Application/Controllers');
define("MODEL_PATH",'Application/Models');
define("VIEW_PATH",'Application/Views');

/*Service path, best to be a child of Controllers*/
define("SERVICE_PATH",'Application/Controllers/Services');
/*Template path, best to be a child of Views*/
define("TEMPLATE_PATH",'Application/Views/Templates');

define("DEFAULT_CONTROLLER", "home");

/*Core framework folder*/
define("PHOENIX",'Framework');

define("PHOENIX_MODE", "dev");

/*DATABASE Connection can be of 'default' or 'ssh'*/
define("DATABASE_CONNECTION_TYPE", "default");

define("ROOT_PATH", "/");