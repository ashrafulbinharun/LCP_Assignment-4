<?php

    session_start();

    require_once __DIR__ . '/vendor/autoload.php';

    use App\Controllers\AuthController;

    $newUser = new AuthController();

    $newUser->register();
    $errors = $newUser->getErrors();
    $oldInput = $newUser->getOldInput();

?>

<!DOCTYPE html>
<html class="h-full bg-white" lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet" />

    <style>
    * {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont,
            'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans',
            'Helvetica Neue', sans-serif;
    }
    </style>

    <title>Create A New Account</title>
</head>

<body class="h-full bg-slate-100">
    <div class="flex flex-col justify-center min-h-full py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-2xl font-bold leading-9 tracking-tight text-center text-gray-900">
                Create A New Account
            </h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            <?php if ( isset( $errors['auth'] ) ): ?>
            <div class="mb-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                <span class="font-bold"><?=$errors['auth'];?></span>
            </div>
            <?php endif;?>

            <div class="px-6 py-12 bg-white shadow sm:rounded-lg sm:px-12">
                <form class="space-y-6" action="<?=htmlspecialchars( $_SERVER['PHP_SELF'] )?>" method="POST">
                    <div>
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                        <div class="mt-2">
                            <input id="name" name="name" type="text"
                                value="<?php echo htmlspecialchars( $oldInput['name'] ?? '' ); ?>" class=" block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1
                                ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset
                                focus:ring-emerald-600 sm:text-sm sm:leading-6 p-2" />
                        </div>
                        <?php if ( isset( $errors['name'] ) ): ?>
                        <p class="text-xs text-red-600 mt-2"><?=$errors['name'];?></p>
                        <?php endif;?>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email
                            Address</label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" autocomplete="email"
                                value="<?php echo htmlspecialchars( $oldInput['email'] ?? '' ); ?>" class=" block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1
                                ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset
                                focus:ring-emerald-600 sm:text-sm sm:leading-6 p-2" />
                        </div>
                        <?php if ( isset( $errors['email'] ) ): ?>
                        <p class="text-xs text-red-600 mt-2"><?=$errors['email'];?></p>
                        <?php endif;?>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 p-2" />
                        </div>
                        <?php if ( isset( $errors['password'] ) ): ?>
                        <p class="text-xs text-red-600 mt-2"><?=$errors['password'];?></p>
                        <?php endif;?>
                    </div>

                    <div>
                        <button type="submit"
                            class="flex w-full justify-center rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                            Register
                        </button>
                    </div>
                </form>
            </div>

            <p class="mt-10 text-sm text-center text-gray-500">
                Already a customer?
                <a href="./login.php" class="font-semibold leading-6 text-emerald-600 hover:text-emerald-500">Sign-in
                </a>
            </p>
        </div>
    </div>
</body>

</html>