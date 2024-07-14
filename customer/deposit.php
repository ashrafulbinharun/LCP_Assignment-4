<?php

    session_start();

    if ( $_SESSION['user']['role'] !== "customer" ) {
        header( "Location: ../login.php" );
        exit;
    }

    require_once __DIR__ . '/../vendor/autoload.php';

    use App\Controllers\TransactionController;
    use App\Helpers\FlashMessage;

    $deposit = new TransactionController();

    $userId = $_SESSION['user']['id'];
    $amount = $_POST['amount'] ?? '';

    $deposit->deposit( $userId, $amount );
    $userDetails = $deposit->userDetails( $userId );

    $balance = $userDetails['balance'];

    $errors = $deposit->getErrors();
    $oldInput = $deposit->getOldInput();

    $flashMsg = FlashMessage::getMessage( 'success' );

?>
<!DOCTYPE html>
<html class="h-full bg-gray-100" lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Tailwindcss CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AlpineJS CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Inter Font -->
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

    <title>Deposit Balance</title>
</head>

<body class="h-full">
    <div class="min-h-full">
        <div class="bg-emerald-600 pb-32">
            <!-- Navigation -->
            <nav class="border-b border-opacity-25 border-emerald-300 bg-emerald-600"
                x-data="{ mobileMenuOpen: false, userMenuOpen: false }">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center px-2 lg:px-0">
                            <div class="hidden sm:block">
                                <div class="flex space-x-4">
                                    <a href="./dashboard.php"
                                        class="px-3 py-2 text-sm font-medium text-white rounded-md hover:bg-emerald-500 hover:bg-opacity-75">Dashboard</a>
                                    <a href="./deposit.php"
                                        class="px-3 py-2 text-sm font-medium text-white rounded-md bg-emerald-700"
                                        aria-current="page">Deposit</a>
                                    <a href="./withdraw.php"
                                        class="px-3 py-2 text-sm font-medium text-white rounded-md hover:bg-emerald-500 hover:bg-opacity-75">Withdraw</a>
                                    <a href="./transfer.php"
                                        class="px-3 py-2 text-sm font-medium text-white rounded-md hover:bg-emerald-500 hover:bg-opacity-75">Transfer</a>
                                </div>
                            </div>
                        </div>
                        <div class="hidden gap-2 sm:ml-6 sm:flex sm:items-center">
                            <!-- Profile dropdown -->
                            <div class="relative ml-3" x-data="{ open: false }">
                                <div>
                                    <button @click="open = !open" type="button"
                                        class="flex text-sm bg-white rounded-full focus:outline-none"
                                        id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                        <span class="sr-only">Open user menu</span>
                                        <span
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100">
                                            <span class="font-medium leading-none text-emerald-700">
                                                <?=strtoupper( substr( $_SESSION['user']['name'], 0, 2 ) )?>
                                            </span>
                                        </span>
                                    </button>
                                </div>

                                <!-- Dropdown menu -->
                                <div x-show="open" @click.away="open = false"
                                    class="absolute right-0 z-10 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                    role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button"
                                    tabindex="-1">
                                    <a href="../logout.php"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem"
                                        tabindex="-1" id="user-menu-item-2">Sign out</a>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center -mr-2 sm:hidden">
                            <!-- Mobile menu button -->
                            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                                class="inline-flex items-center justify-center p-2 rounded-md text-emerald-100 hover:bg-emerald-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500"
                                aria-controls="mobile-menu" aria-expanded="false">
                                <span class="sr-only">Open main menu</span>
                                <!-- Icon when menu is closed -->
                                <svg x-show="!mobileMenuOpen" class="block w-6 h-6" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                </svg>

                                <!-- Icon when menu is open -->
                                <svg x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu, show/hide based on menu state. -->
                <div x-show="mobileMenuOpen" class="sm:hidden" id="mobile-menu">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="./dashboard.php"
                            class="block px-3 py-2 text-base font-medium text-white rounded-md bg-emerald-700"
                            aria-current="page">Dashboard</a>

                        <a href="./deposit.php"
                            class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-emerald-500 hover:bg-opacity-75">Deposit</a>

                        <a href="./withdraw.php"
                            class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-emerald-500 hover:bg-opacity-75">Withdraw</a>

                        <a href="./transfer.php"
                            class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-emerald-500 hover:bg-opacity-75">Transfer</a>
                    </div>
                    <div class="pt-4 pb-3 border-t border-emerald-700">
                        <div class="flex items-center px-5">
                            <div class="flex-shrink-0">
                                <span
                                    class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100">
                                    <span class="font-medium leading-none text-emerald-700">AS</span>
                                </span>
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-medium text-white">
                                    <?=$_SESSION['user']['name']?>
                                </div>
                                <div class="text-sm font-medium text-emerald-300">
                                    <?=$_SESSION['user']['email']?>
                                </div>
                            </div>
                            <button type="button"
                                class="flex-shrink-0 p-1 ml-auto rounded-full bg-emerald-600 text-emerald-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-emerald-600">
                                <span class="sr-only">View notifications</span>
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                            </button>
                        </div>
                        <div class="px-2 mt-3 space-y-1">
                            <a href="../logout.php"
                                class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-emerald-500 hover:bg-opacity-75">Sign
                                out</a>
                        </div>
                    </div>
                </div>
            </nav>

            <header class="py-10">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold tracking-tight text-white">
                        Deposit Balance
                    </h1>
                </div>
            </header>
        </div>

        <main class="-mt-32">
            <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg p-2">
                    <!-- Current Balance Stat -->
                    <dl class="mx-auto grid grid-cols-1 gap-px sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-10 sm:px-6 xl:px-8">
                            <dt class="text-sm font-medium leading-6 text-gray-500">
                                Current Balance
                            </dt>
                            <dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
                                $<?=number_format( $balance, 2 );?>
                            </dd>
                        </div>
                    </dl>

                    <hr />
                    <!-- Deposit Form -->
                    <div class="sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-semibold leading-6 text-gray-800">
                                Deposit Money To Your Account
                            </h3>
                            <div class="mt-4 text-sm text-gray-500">
                                <form action="<?=htmlspecialchars( $_SERVER['PHP_SELF'] )?>" method="POST" novalidate>
                                    <!-- Input Field -->
                                    <div class="relative mt-2 rounded-md">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-0">
                                            <p class="text-gray-400 sm:text-4xl">$</p>
                                        </div>
                                        <input type="number" name="amount" id="amount"
                                            value="<?php echo htmlspecialchars( $oldInput['amount'] ?? '' ); ?>"
                                            class="block w-full ring-0 outline-none text-xl pl-4 py-2 sm:pl-8 text-gray-800 border-b border-b-emerald-500 placeholder:text-gray-400 sm:text-4xl"
                                            placeholder="0.00" />
                                    </div>
                                    <?php if ( isset( $errors['amount'] ) ): ?>
                                    <p class="text-xs text-red-600 mt-2"><?=$errors['amount'];?></p>
                                    <?php endif;?>

                                    <!-- Submit Button -->
                                    <div class="mt-5">
                                        <button type="submit"
                                            class="w-full px-6 py-3.5 text-base font-medium text-white bg-emerald-600 hover:bg-emerald-800 focus:ring-4 focus:outline-none focus:ring-emerald-300 rounded-lg sm:text-xl text-center">
                                            Proceed
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>