<?php

session_start();
require_once './helpers/helper.php';

$errors = [];

if (isset($_SESSION['user_id'])) {
    header('Location:dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validation checking for user's email
    if (empty($email)) {
        $errors['email'] = 'Please enter your email.';
    }
    else {
        $email = sanitize($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email.';
        }
    }

    // Validation checking for user's password
    if (empty($password)) {
        $errors['password'] = 'Please enter your password.';
    }
    elseif (strlen($password) < 8) {
        $errors['password'] = 'Your password must be atleast 8 characters.';
    }
    else {
        $password = sanitize($password);
    }

    if (count($errors) == 0) {

        // Read the users data from file
        $myfile = fopen("data/usersInfo.txt", "r") or die("Unable to open file!");
        $filename = "data/usersInfo.txt";
        $users = file($filename, FILE_IGNORE_NEW_LINES);

        if (count($users) !== 0) {
            foreach ($users as $user) {
                $userData = explode(",", $user);
    
                // Check if any user's data from file storage matches with the provided user's data or not.
                if ($userData[2] == $email && password_verify($password, $userData[3])) {
                    $_SESSION['user_id'] = $userData[0];
                    $_SESSION['user_name'] = $userData[1];
                    header('Location:dashboard.php');
                    exit;
                }
                else {
                    $errors['auth-error'] = 'Invalid email or password.';
                }
            }
    
            fclose($myfile);
        }
        else {
            $errors['auth-error'] = 'Invalid email or password.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TruthWhisper - Anonymous Feedback App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<header class="bg-white">
    <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
        <div class="flex lg:flex-1">
            <a href="./index.php" class="-m-1.5 p-1.5">
                <span class="sr-only">TruthWhisper</span>
                <span class="block font-bold text-lg bg-gradient-to-r from-blue-600 via-green-500 to-indigo-400 inline-block text-transparent bg-clip-text">TruthWhisper</span>
            </a>
        </div>
        <div class="flex lg:hidden">
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
                <span class="sr-only">Open main menu</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>
        <div class="hidden lg:flex lg:flex-1 lg:justify-end">
            <a href="./login.php" class="text-sm font-semibold leading-6 text-gray-900">Log in <span aria-hidden="true">&rarr;</span></a>
        </div>
    </nav>
    <!-- Mobile menu, show/hide based on menu open state. -->
    <div class="lg:hidden" role="dialog" aria-modal="true">
        <!-- Background backdrop, show/hide based on slide-over state. -->
        <div class="fixed inset-0 z-10"></div>
        <div class="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
            <div class="flex items-center justify-between">
                <a href="./index.php" class="-m-1.5 p-1.5">
                    <span class="sr-only">TruthWhisper</span>
                    <span class="block font-bold text-xl bg-gradient-to-r from-blue-600 via-green-500 to-indigo-400 inline-block text-transparent bg-clip-text">TruthWhisper</span>
                </a>
                <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700">
                    <span class="sr-only">Close menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-6 flow-root">
                <div class="-my-6 divide-y divide-gray-500/10">
                    <div class="py-6">
                        <a href="./login.php" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Log in</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<main class="">
    <div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-6 sm:py-12">
        <img src="./images/beams.jpg" alt="" class="absolute top-1/2 left-1/2 max-w-none -translate-x-1/2 -translate-y-1/2" width="1308" />
        <div class="absolute inset-0 bg-[url(./images/grid.svg)] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
        <div class="relative bg-white px-6 pt-10 pb-8 shadow-xl ring-1 ring-gray-900/5 sm:mx-auto sm:max-w-lg sm:rounded-lg sm:px-10">
            <div class="mx-auto max-w-xl">

                <?php
                    if (isset($errors['auth-error'])) :
                ?>

                    <div class="mt-2 bg-red-500 text-sm text-white rounded-lg p-4" role="alert">
                        <span class="font-bold">
                            <?= $errors['auth-error']; ?>
                        </span>
                    </div>

                <?php endif; ?>

                <?php
                    if (isset($_SESSION['flash']['register-success'])) :
                ?>

                    <div class="mt-2 bg-green-500 text-sm text-white rounded-lg p-4" role="success">
                        <span class="font-bold">
                            <?= flashMessage('register-success'); ?>
                        </span>
                    </div>

                <?php endif; ?>


                <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
                    <div class="mx-auto w-full max-w-xl text-center px-24">
                        <h1 class="block text-center font-bold text-2xl bg-gradient-to-r from-blue-600 via-green-500 to-indigo-400 inline-block text-transparent bg-clip-text">TruthWhisper</h1>
                    </div>
                    <div class="mt-10 mx-auto w-full max-w-xl">
                        <form class="space-y-6" action="login.php" method="POST" novalidate>
                            <div>
                                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                                <div class="mt-2">
                                    <input id="email" name="email" type="email" autocomplete="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    
                                    <?php 
                                     if (isset($errors['email'])) :
                                    ?>
                                        <p class="text-xs text-red-600 mt-2"><?= $errors['email']; ?></p>
                                    <?php endif; ?>

                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                                    <div class="text-sm">
                                        <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <input id="password" name="password" type="password" autocomplete="current-password" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">

                                    <?php 
                                     if (isset($errors['password'])) :
                                    ?>
                                        <p class="text-xs text-red-600 mt-2"><?= $errors['password']; ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div>
                                <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign in</button>
                            </div>
                        </form>

                        <p class="mt-10 text-center text-sm text-gray-500">
                            Not a member?
                            <a href="./register.php" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Register now!</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="bg-white">
    <div class="mx-auto max-w-7xl px-6 py-12 md:flex md:items-center justify-center lg:px-8">
        <p class="text-center text-xs leading-5 text-gray-500">&copy; 2024 TruthWhisper, Inc. All rights reserved.</p>
    </div>
</footer>

</body>
</html>