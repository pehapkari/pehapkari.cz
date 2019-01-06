[1mdiff --git a/projects/open-training/bin/console b/projects/open-training/bin/console[m
[1mindex 466c348..92a74c2 100755[m
[1m--- a/projects/open-training/bin/console[m
[1m+++ b/projects/open-training/bin/console[m
[36m@@ -10,10 +10,6 @@[m [mset_time_limit(0);[m
 [m
 require getcwd() . '/vendor/autoload.php';[m
 [m
[31m-if (!class_exists(Application::class)) {[m
[31m-    throw new RuntimeException('You need to add "symfony/framework-bundle" as a Composer dependency.');[m
[31m-}[m
[31m-[m
 $input = new ArgvInput();[m
 if (null !== $env = $input->getParameterOption(['--env', '-e'], null, true)) {[m
     putenv('APP_ENV='.$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = $env);[m
[36m@@ -23,6 +19,8 @@[m [mif ($input->hasParameterOption('--no-debug', true)) {[m
     putenv('APP_DEBUG='.$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = '0');[m
 }[m
 [m
[32m+[m[32mrequire dirname(__DIR__).'/config/bootstrap.php';[m
[32m+[m
 if ($_SERVER['APP_DEBUG']) {[m
     umask(0000);[m
 [m
[1mdiff --git a/projects/open-training/config/bootstrap.php b/projects/open-training/config/bootstrap.php[m
[1mindex 74e1742..fdab9a3 100644[m
[1m--- a/projects/open-training/config/bootstrap.php[m
[1m+++ b/projects/open-training/config/bootstrap.php[m
[36m@@ -7,8 +7,6 @@[m [muse Symfony\Component\Dotenv\Dotenv;[m
 if (is_array($env = @include dirname(__DIR__).'/.env.local.php')) {[m
     $_SERVER += $env;[m
     $_ENV += $env;[m
[31m-} elseif (!class_exists(Dotenv::class)) {[m
[31m-    throw new RuntimeException('Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.');[m
 } else {[m
     // load all the .env files[m
     (new Dotenv())->loadEnv(dirname(__DIR__).'/.env');[m
