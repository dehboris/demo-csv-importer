<?php

namespace Tests\Services\CSVImporter\Pipes;

use App\CSVRow;
use App\Participant;
use App\Services\CSVImporter\CSVImportTraveler;
use App\Services\CSVImporter\Pipes\AssignEmergencyContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignEmergencyContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_and_assigns_an_emergency_contact_for_an_imported_participant()
    {
        $participant = factory(Participant::class)->create();

        $rowContents = [
            'emergency_contact_name' => 'John Doe',
            'emergency_contact_phone' => '555-555-5555'
        ];

        $csvRow = factory(CSVRow::class)->create(['contents' => $rowContents]);

        $traveler = (new CSVImportTraveler())->setRow($csvRow)
            ->setParticipant($participant);

        (new AssignEmergencyContact())->handle($traveler, function () {});

        $this->assertDatabaseHas('emergency_contacts', [
            'name' => 'John Doe',
            'phone' => '555-555-5555'
        ]);

        $this->assertEquals('John Doe', $participant->emergencyContact->name);
        $this->assertEquals('555-555-5555', $participant->emergencyContact->phone);
    }
}