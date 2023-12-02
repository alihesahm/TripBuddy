<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

function unsetArrayEmptyParam(array $array): array
{
    foreach ($array as $key => $value) {
        if (! isset($array[$key])) {

            unset($array[$key]);
        }
    }

    return $array;
}

function generateOtp(): int
{
    return random_int(1000, 9999);
}

function currentUser(): User
{
    return Auth::guard('sanctum')->user();
}

function removeTags(string $string): string
{
    $text = preg_replace('/<[^>]*>/', '', $string); // remove tabs
    $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);  // remove symbols
    $text = preg_replace("/\r|\n/", ' ', $text); // remove newline (\n\r)
    $text = str_replace('nbsp', '', $text); // remove nbsp

    return $text;
}

function isFile($file): bool
{
    return $file instanceof UploadedFile;
}

function isAuthenticated(): ?bool
{
    return Auth::guard('sanctum')->check();
}

function internationalPhone($country_code, $phone): string
{
    return '+'.$country_code.$phone;
}
