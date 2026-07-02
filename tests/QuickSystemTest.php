<?php

namespace Tests;

use App\Models\AuditLog;
use App\Models\Device;
use App\Models\Procedure;
use App\Models\Server;
use App\Models\User;
use App\Models\WorklistItem;
use Database\Seeders\ProcedureSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class QuickSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProcedureSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_models_have_correct_data(): void
    {
        $this->assertEquals(24, Procedure::count(), '24 procedures seeded');
        $this->assertEquals(6, Role::count(), '6 default roles');
        $this->assertEquals(37, Permission::count(), '37 permissions');
    }

    public function test_procedure_catalog_has_all_modalities(): void
    {
        $modalities = Procedure::select('modality')->distinct()->pluck('modality')->toArray();
        $this->assertContains('DX', $modalities);
        $this->assertContains('CT', $modalities);
        $this->assertContains('MR', $modalities);
        $this->assertContains('US', $modalities);
        $this->assertContains('MG', $modalities);
    }

    public function test_worklist_item_status_constants(): void
    {
        $this->assertEquals('registered', WorklistItem::STATUS_REGISTERED);
        $this->assertEquals('mw_published', WorklistItem::STATUS_MW_PUBLISHED);
        $this->assertEquals('acquiring', WorklistItem::STATUS_ACQUIRING);
        $this->assertEquals('acquired', WorklistItem::STATUS_ACQUIRED);
        $this->assertEquals('sent_to_pacs', WorklistItem::STATUS_SENT_TO_PACS);
        $this->assertEquals('archived', WorklistItem::STATUS_ARCHIVED);
        $this->assertEquals('reported', WorklistItem::STATUS_REPORTED);
        $this->assertEquals('verified', WorklistItem::STATUS_VERIFIED);
        $this->assertEquals('cancelled', WorklistItem::STATUS_CANCELLED);
        $this->assertEquals('failed', WorklistItem::STATUS_FAILED);
    }

    public function test_worklist_item_status_helpers(): void
    {
        $this->assertEquals('MWL Published', WorklistItem::statusLabel('mw_published'));
        $this->assertStringContainsString('bg-blue-100', WorklistItem::statusColor('mw_published'));
        $this->assertEquals('Unknown', WorklistItem::statusLabel('unknown'));
        $this->assertStringContainsString('bg-gray-100', WorklistItem::statusColor('unknown'));
    }

    public function test_device_model_has_monitor_columns(): void
    {
        $fillable = (new Device)->getFillable();
        $this->assertContains('last_echo_at', $fillable);
        $this->assertContains('queue_count', $fillable);
    }

    public function test_audit_log_model(): void
    {
        $fillable = (new AuditLog)->getFillable();
        $this->assertContains('method', $fillable);
        $this->assertContains('endpoint', $fillable);
        $this->assertContains('response_status', $fillable);
    }

    public function test_all_routes_exist(): void
    {
        $routes = collect(Route::getRoutes()->getRoutesByName());

        // Fase 1
        $this->assertTrue($routes->has('registration.index'));
        $this->assertTrue($routes->has('worklist.index'));
        $this->assertTrue($routes->has('mwl-queue.index'));
        $this->assertTrue($routes->has('study-tracker.index'));
        $this->assertTrue($routes->has('modality-monitor.index'));
        $this->assertTrue($routes->has('pacs-monitor.index'));
        $this->assertTrue($routes->has('studies.poll'));
        $this->assertTrue($routes->has('settings.procedures.index'));

        // Fase 2
        $this->assertTrue($routes->has('admin.users.index'));
        $this->assertTrue($routes->has('admin.roles.index'));
        $this->assertTrue($routes->has('admin.audit-logs.index'));
        $this->assertTrue($routes->has('admin.jobs.index'));

        // Fase 3
        $this->assertTrue($routes->has('utilities.dicom.index'));
        $this->assertTrue($routes->has('utilities.health'));
        $this->assertTrue($routes->has('utilities.log-viewer'));

        // Fase 4: navigation anchors
        $this->assertTrue($routes->has('settings.index'));
    }

    public function test_commands_registered(): void
    {
        $commands = Artisan::all();
        $this->assertArrayHasKey('ris:poll-studies', $commands);
        $this->assertArrayHasKey('ris:ping-modalities', $commands);
    }

    public function test_schedule_defined(): void
    {
        $console = file_get_contents(base_path('routes/console.php'));
        $this->assertStringContainsString('ris:poll-studies', $console);
        $this->assertStringContainsString('ris:ping-modalities', $console);
    }

    public function test_views_compile(): void
    {
        $views = [
            'dashboard',
            'registration.index',
            'worklist.index',
            'mwl-queue.index',
            'study-tracker.index',
            'study-tracker.show',
            'modality-monitor.index',
            'pacs-monitor.index',
            'settings.procedures.index',
            'settings.procedures.form',
            'settings.index',
            'admin.users.index',
            'admin.users.form',
            'admin.roles.index',
            'admin.roles.form',
            'admin.audit-logs.index',
            'admin.audit-logs.show',
            'admin.jobs.index',
            'utilities.dicom-tools',
            'utilities.health',
            'utilities.log-viewer',
        ];

        foreach ($views as $view) {
            try {
                view($view);
                $this->assertTrue(true);
            } catch (\Exception $e) {
                $this->fail("View [$view] failed: " . $e->getMessage());
            }
        }
    }

    public function test_schedule_commands_execute(): void
    {
        Server::create(['name' => 'Test', 'base_url' => 'http://localhost:8080', 'aet' => 'DCM4CHEE', 'archive' => 'dcm4chee-arc', 'username' => 'test', 'password' => 'test', 'enabled' => true]);
        $exitCode = Artisan::call('ris:poll-studies');
        $this->assertEquals(0, $exitCode, 'ris:poll-studies exits cleanly');

        $exitCode = Artisan::call('ris:ping-modalities');
        $this->assertEquals(0, $exitCode, 'ris:ping-modalities exits cleanly');
    }

    public function test_http_pages_return_200(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        // Need a server for /studies/poll
        Server::create(['name' => 'Test', 'base_url' => 'http://localhost:8080', 'aet' => 'DCM4CHEE', 'archive' => 'dcm4chee-arc', 'username' => 'test', 'password' => 'test', 'enabled' => true]);

        $pages = [
            '/dashboard',
            '/registration',
            '/worklist',
            '/mwl-queue',
            '/study-tracker',
            '/modality-monitor',
            '/pacs-monitor',
            '/settings',
            '/settings/procedures',
            '/settings/procedures/create',
            '/admin/users',
            '/admin/roles',
            '/admin/audit-logs',
            '/admin/jobs',
            '/utilities/dicom',
            '/utilities/health',
            '/utilities/logs',
        ];

        foreach ($pages as $page) {
            $response = $this->actingAs($user)->get($page);
            $this->assertEquals(200, $response->status(), "Page [$page] returned " . $response->status());
        }

        // /studies/poll skipped — it's an action endpoint, not a page
    }

    public function test_procedure_create_via_http(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $response = $this->actingAs($user)->post('/settings/procedures', [
            'code' => 'TEST_001',
            'name' => 'Test Procedure',
            'modality' => 'DX',
            'description' => 'A test procedure',
            'body_part' => 'CHEST',
            'estimated_duration' => 10,
        ]);
        $response->assertStatus(302);
        $this->assertEquals(25, Procedure::count());
    }

    public function test_role_crud_via_http(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        // Create role
        $response = $this->actingAs($user)->post('/admin/roles', [
            'name' => 'TestRole',
            'permissions' => [Permission::first()->id],
        ]);
        $response->assertSessionHas('success');
        $this->assertEquals(7, Role::count());
    }

    public function test_user_crud_via_http(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        // Create user
        $response = $this->actingAs($user)->post('/admin/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => [Role::where('name', 'Operator')->first()->id],
        ]);
        $response->assertSessionHas('success');
        $this->assertEquals(2, User::count());
    }

    public function test_workflow_item_persistence(): void
    {
        $procedure = Procedure::first();

        $server = Server::create(['name' => 'Test', 'base_url' => 'http://localhost:8080', 'aet' => 'DCM4CHEE', 'archive' => 'dcm4chee-arc', 'username' => 'test', 'password' => 'test', 'enabled' => true]);
        $item = WorklistItem::create([
            'server_id' => $server->id,
            'accession_number' => 'ACC-001',
            'patient_name' => 'Test Patient',
            'patient_id' => 'P-001',
            'modality' => $procedure->modality,
            'procedure_code' => $procedure->code,
            'procedure_description' => $procedure->name,
            'requesting_physician' => 'dr. Test',
            'scheduled_date' => now()->format('Ymd'),
            'scheduled_time' => now()->format('His'),
            'status' => WorklistItem::STATUS_MW_PUBLISHED,
        ]);

        $this->assertNotNull($item->id);
        $this->assertEquals(WorklistItem::STATUS_MW_PUBLISHED, $item->status);
        $this->assertEquals($procedure->code, $item->procedure_code);

        // Status transitions
        $item->update(['status' => WorklistItem::STATUS_ACQUIRING]);
        $this->assertEquals(WorklistItem::STATUS_ACQUIRING, $item->fresh()->status);

        $item->update(['status' => WorklistItem::STATUS_ACQUIRED]);
        $this->assertEquals(WorklistItem::STATUS_ACQUIRED, $item->fresh()->status);
    }
}
