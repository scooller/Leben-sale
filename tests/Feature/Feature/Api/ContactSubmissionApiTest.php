<?php

namespace Tests\Feature\Feature\Api;

use App\Mail\ContactSubmissionReceivedMail;
use App\Models\ContactSubmission;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactSubmissionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_contact_submission_and_sends_email_to_configured_recipient(): void
    {
        Mail::fake();

        SiteSetting::current()->update([
            'contact_form_fields' => [
                ['key' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
                ['key' => 'rut', 'label' => 'RUT', 'type' => 'text', 'required' => false],
                ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['key' => 'message', 'label' => 'Mensaje', 'type' => 'textarea', 'required' => true],
            ],
            'contact_notification_email' => 'leads@ileben.cl',
        ]);

        $response = $this->postJson('/api/v1/contact-submissions', [
            'fields' => [
                'name' => 'Juan Perez',
                'rut' => '12.345.678-9',
                'email' => 'juan@example.com',
                'message' => 'Quiero información del proyecto.',
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Tu mensaje fue enviado correctamente.');

        $this->assertDatabaseHas('contact_submissions', [
            'name' => 'Juan Perez',
            'email' => 'juan@example.com',
            'rut' => '12.345.678-9',
            'recipient_email' => 'leads@ileben.cl',
        ]);

        $submission = ContactSubmission::query()->first();

        $this->assertNotNull($submission);
        $this->assertSame('Quiero información del proyecto.', $submission->fields['message']);

        Mail::assertSent(ContactSubmissionReceivedMail::class, function (ContactSubmissionReceivedMail $mail) {
            return $mail->hasTo('leads@ileben.cl');
        });
    }

    public function test_it_validates_required_dynamic_fields(): void
    {
        SiteSetting::current()->update([
            'contact_form_fields' => [
                ['key' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
                ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
            ],
        ]);

        $response = $this->postJson('/api/v1/contact-submissions', [
            'fields' => [
                'name' => 'Sin Email',
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fields.email']);
    }
}
