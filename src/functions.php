<?php

declare(strict_types=1);

namespace Mos\Functions;

use Elmittil\Dice\Dice;
use Elmittil\Dice\DiceHand;
use Elmittil\Dice\GraphicDice;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

/**
 * Functions.
 */


/**
 * Get the route path representing the page being requested.
 *
 * @return string with the route path requested.
 */
function getRoutePath(): string
{
    $offset = strlen(dirname($_SERVER["SCRIPT_NAME"]) ?? null);
    $path   = substr($_SERVER["REQUEST_URI"] ?? "", $offset);

    return $path ? $path : "";
}



/**
 * Render the view and return its rendered content.
 *
 * @param string $template to use when rendering the view.
 * @param array  $data     send to as variables to the view.
 *
 * @return string with the route path requested.
 */
function renderView(
    string $template,
    array $data = []
): string {
    extract($data);

    ob_start();
    require INSTALL_PATH . "/view/$template";
    $content = ob_get_contents();
    ob_end_clean();

    return ($content ? $content : "");
}



/**
 * Use Twig to render a view and return its rendered content.
 *
 * @param string $template to use when rendering the view.
 * @param array  $data     send to as variables to the view.
 *
 * @return string with the route path requested.
 */
function renderTwigView(
    string $template,
    array $data = []
): string {
    static $loader = null;
    static $twig = null;

    if (is_null($twig)) {
        $loader = new FilesystemLoader(
            INSTALL_PATH . "/view/twig"
        );
        // $twig = new \Twig\Environment($loader, [
        //     "cache" => INSTALL_PATH . "/cache/twig",
        // ]);
        $twig = new Environment($loader);
    }

    return $twig->render($template, $data);
}



// /**
//  * Send a response to the client.
//  *
//  * @param int    $status   HTTP status code to send to client.
//  *
//  * @return void
//  */
// function sendResponse(string $body, int $status = 200): void
// {
//     http_response_code($status);
//     echo $body;
// }



// /**
//  * Redirect to an url.
//  *
//  * @param string $url where to redirect.
//  *
//  * @return void
//  */
// function redirectTo(string $url): void
// {
//     http_response_code(200);
//     header("Location: $url");
// }



/**
 * Create an url into the website using the path and prepend the baseurl
 * to the current website.
 *
 * @param string $path to use to create the url.
 *
 * @return string with the route path requested.
 */
function url(string $path): string
{
    return getBaseUrl() . $path;
}



/**
 * Get the base url from the request, relative to the htdoc/ directory.
 *
 * @return string as the base url.
 */
function getBaseUrl()
{
    static $baseUrl = null;

    if ($baseUrl) {
        return $baseUrl;
    }

    $scriptName = rawurldecode($_SERVER["SCRIPT_NAME"]);
    $path = rtrim(dirname($scriptName), "/");

    // Prepare to create baseUrl by using currentUrl
    $parts = parse_url(getCurrentUrl());

     // Build the base url from its parts
     $siteUrl = ($parts["scheme"] ?? null)
     . "://"
     . ($parts["host"] ?? null)
     . (isset($parts["port"])
         ? ":{$parts["port"]}"
         : "");
    $baseUrl = $siteUrl . $path;

    return $baseUrl;
}



/**
 * Get the current url of the request.
 *
 * @return string as current url.
 */
function getCurrentUrl(): string
{
    $scheme = $_SERVER["REQUEST_SCHEME"] ?? "";
    $server = $_SERVER["SERVER_NAME"] ?? "";

    $port  = $_SERVER["SERVER_PORT"] ?? "";
    $port  = ($port === "80")
        ? ""
        : (($port === 443 && $_SERVER["HTTPS"] === "on")
            ? ""
            : ":" . $port);

    $uri = rtrim(rawurldecode($_SERVER["REQUEST_URI"] ?? ""), "/");

    $url  = htmlspecialchars($scheme) . "://";
    $url .= htmlspecialchars($server)
        . $port . htmlspecialchars(rawurldecode($uri));

    return $url;
}



/**
 * Destroy the session.
 *
 * @return void
 */
function destroySession(): void
{
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
}


function buttonRoll(int $diceQty): void
{
    $hand = setupAndRoll($diceQty);
    $_SESSION['roll'][0] =  $hand;
    $_SESSION['total'][0] = $_SESSION['total'][0] + $hand;

    if (checkIfOver21("COMPUTER", $_SESSION['total'][0])) {
        return;
    }

    if (shouldComputerRoll($_SESSION['total'][0], $_SESSION['total'][1])) {
        $hand = setupAndRoll($diceQty);
        $_SESSION['roll'][1] =  $hand;
        $_SESSION['total'][1] = $_SESSION['total'][1] + $hand;
    }

    if (checkIfOver21("YOU", $_SESSION['total'][1])) {
        return;
    }

    if (checkIf21($_SESSION['total'][1])) {
        return;
    }
}

function buttonPass(int $diceQty): void
{
    while ($_SESSION['total'][1] <= $_SESSION['total'][0]) {
        $hand = setupAndRoll($diceQty);
        $_SESSION['roll'][1] =  $hand;
        $_SESSION['total'][1] = $_SESSION['total'][1] + $hand;
    }

    if ($_SESSION['total'][1] <= 21) {
        $_SESSION['message'] = "COMPUTER WON!!! <p><a href='" . url('/game21/reset') . "'><input type='submit' class='new-game-button' value='NEXT ROUND'/></a></p>";
        array_push($_SESSION['score'], ["", "x"]);
        return;
    }

    $_SESSION['message'] = "YOU WON!!! <p><a href='" . url('/game21/reset') . "'><input type='submit' class='new-game-button' value='NEXT ROUND'/></a></p>";
    array_push($_SESSION['score'], ["x", ""]);
    return;
}

function resetGame()
{
    $_SESSION['roll'] = array(0 , 0);
    $_SESSION['total'] = array(0 , 0);
    $_SESSION['message'] = "";
}

function setupAndRoll(int $diceQty): int
{
    $hand = new DiceHand($diceQty, "regular");
    $hand->roll($diceQty);
    $rolled =  $hand->getRollSum();
    return $rolled;
}

function shouldComputerRoll(int $playerScore, int $computerScore): bool
{
    if ($computerScore < 21 && $computerScore < $playerScore) {
        return true;
    }
    return false;
}

function checkIfOver21(string $who, int $total): bool
{
    if ($total > 21) {
        $_SESSION['message'] = $who . " WON!!! <p><a href='" . url('/game21/reset') . "'><input type='submit' class='new-game-button' value='NEXT ROUND'/></a></p>";
        if ($who === "COMPUTER") {
            array_push($_SESSION['score'], ["", "x"]);
        }
        if ($who === "YOU") {
            array_push($_SESSION['score'], ["x", ""]);
        }
        return true;
    }
    return false;
}

function checkIf21(int $total): bool
{
    if ($total == 21) {
        $_SESSION['message'] = "COMPUTER WON!!! <p><a href='" . url('/game21/reset') . "'><input type='submit' class='new-game-button' value='NEXT ROUND'/></a></p>";
        array_push($_SESSION['score'], ["", "x"]);
        return true;
    }
    return false;
}
