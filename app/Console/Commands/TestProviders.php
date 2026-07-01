<?php

namespace App\Console\Commands;

use App\AI\AIManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestProviders extends Command
{
    protected $signature = 'test:providers';
    protected $description = 'Test all AI providers and report which ones work';

    public function handle(AIManager $manager): int
    {
        $this->newLine();
        $this->info('Probando proveedores con API key configurada...');
        $this->newLine();

        $working = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($manager->getAllProviders() as $provider) {
            $slug = $provider->getSlug();
            $name = $provider->getName();

            if (!$provider->isAvailable()) {
                $this->line("  ⏭️  {$name}" . str_repeat(' ', 14 - strlen($name)) . "| (sin API key o no disponible) | SKIP");
                $skipped++;
                continue;
            }

            $models = $provider->getAvailableModels();
            $testModel = $models[0]['id'] ?? 'unknown';

            $this->info("  Probando {$name} ({$testModel})...");

            try {
                $start = microtime(true);
                $response = $provider->sendMessage(
                    [['role' => 'user', 'content' => 'Responde solo hola']],
                    ['model' => $testModel, 'max_tokens' => 50]
                );
                $elapsed = round(microtime(true) - $start, 1);

                if (empty(trim($response->content))) {
                    $this->error("  ❌ {$name}" . str_repeat(' ', 14 - strlen($name)) . "| {$testModel} | {$elapsed}s | Respuesta vacía");
                    $failed++;
                } else {
                    $preview = mb_substr(trim($response->content), 0, 40);
                    $this->info("  ✅ {$name}" . str_repeat(' ', 14 - strlen($name)) . "| {$testModel} | {$elapsed}s | {$preview}");
                    $working++;
                }
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();

                Log::error("test:providers failed for {$name}", [
                    'provider' => $slug,
                    'model' => $testModel,
                    'error' => $errorMsg,
                ]);

                if (strlen($errorMsg) > 60) {
                    $errorMsg = mb_substr($errorMsg, 0, 57) . '...';
                }
                $this->error("  ❌ {$name}" . str_repeat(' ', 14 - strlen($name)) . "| {$testModel} | {$errorMsg}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Resumen: {$working} funcionando, {$failed} fallando, {$skipped} sin key/disponibles");
        $this->newLine();

        $this->info('Logs detallados guardados en storage/logs/laravel.log');
        $this->info('Para verlos: tail -100 storage/logs/laravel.log | grep "API response"');
        $this->newLine();

        return $working > 0 ? self::SUCCESS : self::FAILURE;
    }
}
