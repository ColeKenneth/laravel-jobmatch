<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Certification;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class CertificationService
{
    public function create(array $data) : Certification
    {
        $student = Student::findOrFail($data['student_id']);

        $issued = Carbon::parse($data['date_issued']);
        $expiry = isset($data['expiry_date']) ? Carbon::parse($data['expiry_date']) : null;

        if ($expiry !== null && $expiry->lte($issued)) {
            throw new BusinessRuleException("Expiry date must be after the issued date.");
        }

        $attributes = [
            'student_id' => $data['student_id'],
            'name' => trim($data['name']),
            'issuing_authority' => trim($data['issuing_authority']),
            'date_issued' => $issued,
            'expiry_date' => $expiry,
            'verification_url' => isset($data['verification_url']) ? trim($data['verification_url']) : null,
            'credential_id' => isset($data['credential_id']) ? trim($data['credential_id']) : null,
        ];

        $certification = Certification::create($attributes);

        Log::info("Created certification for student", ['id' => $certification->id, 'student_id' => $student->id]);

        return $certification;
    }

    public function update(int $id, array $data) : Certification
    {
        $certification = Certification::findOrFail($id);

        $issued = Carbon::parse($data['date_issued']);
        $expiry = isset($data['expiry_date']) ? Carbon::parse($data['expiry_date']) : null;

        if ($expiry !== null && $expiry->lte($issued)) {
            throw new BusinessRuleException("Expiry date must be after the issued date.");
        }

        if (!empty($data['student_id']) && (int)$data['student_id'] !== (int)$certification->student_id) {
            throw new BusinessRuleException("Cannot reassign a certification to another student.");
        }

        $certification->fill([
            'name' => trim($data['name']),
            'issuing_authority' => trim($data['issuing_authority']),
            'date_issued' => $issued,
            'expiry_date' => $expiry,
            'verification_url' => isset($data['verification_url']) ? trim($data['verification_url']) : null,
            'credential_id' => isset($data['credential_id']) ? trim($data['credential_id']) : null,
        ]);

        $certification->save();

        Log::info("Updated certification", ['id' => $certification->id]);

        return $certification;
    }

    public function delete(int $id) : void
    {
        $certification = Certification::findOrFail($id);
        $certification->delete();

        Log::info("Deleted certification", ['id' => $id]);
    }

    public function getById(int $id) : Certification
    {
        return Certification::with('student')->findOrFail($id);
    }

    public function getAll() : Collection
    {
        return Certification::with('student')
                            ->latest('date_issued')
                            ->get();
    }
}
