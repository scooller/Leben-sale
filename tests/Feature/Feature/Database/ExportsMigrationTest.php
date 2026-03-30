<?php

namespace Tests\Feature\Feature\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExportsMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_exports_table_exists_with_required_columns(): void
    {
        $this->assertTrue(Schema::hasTable('exports'));

        $this->assertTrue(Schema::hasColumns('exports', [
            'id',
            'completed_at',
            'file_disk',
            'file_name',
            'exporter',
            'processed_rows',
            'total_rows',
            'successful_rows',
            'user_id',
            'created_at',
            'updated_at',
        ]));
    }
}
