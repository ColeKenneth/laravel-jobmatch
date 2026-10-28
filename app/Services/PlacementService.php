<?php
declare(strict_types=1);

namespace App\Services;

use App\Enum\PlacementStatus;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Models\JobPosting;
use App\Models\PlacementRecord;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class PlacementService
{
    public function create(int $studentId, array $data) : PlacementRecord
    {
        Student::findOrFail($studentId);
        JobPosting::findOrFail($data['job_posting_id']);

        $alreadyExists = PlacementRecord::where('student_id', $studentId)
            ->where('job_posting_id', $data['job_posting_id'])
            ->exists();

        if ($alreadyExists) {
            throw new AlreadyExistsException("A placement record already exists for this student and job posting.");
        }

        $attributes = [
          'student_id' => $studentId,
          'job_posting_id' => $data['job_posting_id'],
          'placement_date' => Carbon::parse($data['placement_date']),
          'placement_status' => trim($data['placement_status']),
          'notes' => isset($data['notes']) ? trim($data['notes']) : null,
        ];

        $placementRecord = PlacementRecord::create($attributes);

        Log::info("Created placement record", [
            'student_id' => $studentId,
            'job_posting_id' => $placementRecord->job_posting_id,
        ]);

        return $placementRecord;
    }

    public function delete(int $id) : void
    {
        $placementRecord = PlacementRecord::findOrFail($id);
        $placementRecord->delete();

        Log::info("Deleted placement record", ['id' => $id]);
    }

    public function exists(int $id) : bool
    {
        return PlacementRecord::where('id', $id)->exists();
    }

    public function getByJobPosting(int $jobPosting) : Collection
    {
        return PlacementRecord::where('job_posting_id', $jobPosting)
            ->latest('placement_date')
            ->get();
    }

    public function getByStatus(PlacementStatus $status) : Collection
    {
        return PlacementRecord::where('placement_status', $status)
            ->latest('placement_date')
            ->get();
    }

    public function getByStudent(int $student) : Collection
    {
        return PlacementRecord::where('student_id', $student)
            ->latest('placement_date')
            ->get();
    }

    public function update(int $id, array $data) : PlacementRecord
    {
        $placementRecord = PlacementRecord::findOrFail($id);

        if (!empty($data['student_id']) && (int)$data['student_id'] !== (int)$placementRecord->student_id) {
            throw new BusinessRuleException("Cannot reassign a placement record to a different student.");
        }

        if (!empty($data['job_posting_id']) && (int)$data['job_posting_id'] !== (int)$placementRecord->job_posting_id) {
            throw new BusinessRuleException("Cannot reassign a placement record to a different job posting.");
        }

        $placementRecord->fill([
            'placement_date' => Carbon::parse($data['placement_date']),
            'placement_status' => $data['placement_status'],
            'notes' => isset($data['notes']) ? trim($data['notes']) : null,
        ]);

        $placementRecord->save();

        Log::info("Updated placement record", ['id' => $id]);

        return $placementRecord;
    }

    public function updateStatus(int $id, PlacementStatus $status) : void
    {
        $updatedCount = (int)PlacementRecord::where('id', $id)
            ->update(['placement_status' => $status]);

        if ($updatedCount === 0) {
            throw new ModelNotFoundException()->setModel(PlacementRecord::class, [$id]);
        }

        Log::info("Updated placement status", ['id' => $id]);
    }
}
