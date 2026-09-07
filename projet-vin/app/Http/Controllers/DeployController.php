<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeployController extends Controller
{
    /**
     * Remet l'application en état après l'envoi FTPS par GitHub Actions.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $token = config('deploy.token');

        if (! is_string($token) || $token === '') {
            abort(404);
        }

        if (! hash_equals($token, (string) $request->header('X-Deploy-Token'))) {
            abort(404);
        }

        @set_time_limit(300);

        $steps = [];
        $failed = false;

        $this->ensureWritableDirectories();

        foreach ($this->artisanCommands() as $step) {
            $result = $this->runArtisan($step['command'], $step['options']);
            $steps[] = $result;

            if ($result['status'] !== 0 && ! $step['optional']) {
                $failed = true;

                break;
            }
        }

        $context = ['version' => $request->header('X-Deploy-Version'), 'steps' => $steps];

        $failed
            ? Log::error('Déploiement interrompu.', $context)
            : Log::info('Déploiement terminé.', $context);

        return response()->json([
            'ok' => ! $failed,
            'version' => $request->header('X-Deploy-Version'),
            'steps' => $steps,
        ], $failed ? 500 : 200);
    }

    /**
     * @return list<array{command: string, options: array<string, bool>, optional: bool}>
     */
    private function artisanCommands(): array
    {
        return [
            ['command' => 'optimize:clear', 'options' => [], 'optional' => false],
            ['command' => 'migrate', 'options' => ['--force' => true], 'optional' => false],
            ['command' => 'storage:link', 'options' => ['--force' => true], 'optional' => true],
            ['command' => 'optimize', 'options' => [], 'optional' => false],
        ];
    }

    /**
     * @param  array<string, bool>  $options
     * @return array{command: string, status: int, output: string}
     */
    private function runArtisan(string $command, array $options): array
    {
        try {
            $status = Artisan::call($command, $options);
            $output = Artisan::output();
        } catch (Throwable $exception) {
            $status = 1;
            $output = $exception->getMessage();
        }

        return [
            'command' => 'artisan '.$command,
            'status' => $status,
            'output' => trim($output),
        ];
    }

    /**
     * L'envoi FTPS ignore storage/ et bootstrap/cache/ pour préserver les
     * données serveur. Les dossiers nécessaires sont donc recréés ici.
     */
    private function ensureWritableDirectories(): void
    {
        $directories = [
            storage_path('app/private'),
            storage_path('app/public'),
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
            base_path('bootstrap/cache'),
        ];

        foreach ($directories as $directory) {
            File::ensureDirectoryExists($directory);
        }
    }
}
