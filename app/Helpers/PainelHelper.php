// app/Helpers/PainelHelper.php
namespace App\Helpers;

class PainelHelper
{
    public static function isAdmin(): bool
    {
        return auth()->user()?->nivel_acesso === 'admin';
    }

    public static function isModerador(): bool
    {
        return in_array(auth()->user()?->nivel_acesso, ['admin', 'moderador']);
    }
}
