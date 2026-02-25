<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestTransbankConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-transbank-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Transbank Mall configuration';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('=== Testing Transbank Configuration ===');

        // Test 1: Get config
        $config = config('payments.gateways.transbank');
        $this->line('Config loaded: '.(is_array($config) ? 'YES' : 'NO'));

        if (! is_array($config)) {
            $this->error('Configuration is not an array! Value: '.gettype($config));

            return;
        }

        $this->line('Mall mode: '.($config['mall_mode'] ? 'ENABLED' : 'DISABLED'));

        // Test 2: Get commerce codes
        $codes = $config['commerce_codes'] ?? [];
        $this->info('Commerce codes count: '.count($codes));

        if (count($codes) > 0) {
            $this->line("\nFirst 5 codes:");
            foreach (array_slice($codes, 0, 5, true) as $slug => $code) {
                $this->line("  $slug => $code");
            }
            $this->info("\n✅ Configuration is working correctly!");
        } else {
            $this->error("\n⚠️  NO CODES FOUND!");
            $this->line('Raw env value:');
            $this->line('  '.(env('TRANSBANK_STORE_CODES') ?: 'NOT SET'));
        }
    }
}
