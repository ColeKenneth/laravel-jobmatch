<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Alumni;
use App\Models\EmploymentHistory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class EmploymentHistoryService
{
   private function validateEmploymentDates(array $data) : void
   {
       $isCurrent = filter_var($data['is_current'], FILTER_VALIDATE_BOOLEAN);
       $startDate = Carbon::parse($data['start_date']);
       $endDate = isset($data['end_date']) ? Carbon::parse($data['end_date']) : null;

       if ($isCurrent && $endDate !== null) {
           throw new BusinessRuleException("A current position cannot have an end date.");
       }

       if (!$isCurrent && $endDate === null) {
           throw new BusinessRuleException("A past position must have an end date.");
       }

       if ($endDate !== null && $endDate->lt($startDate)) {
           throw new BusinessRuleException("End date must be on or after the start date.");
       }
   }

   public function create(array $data) : EmploymentHistory
   {
       $alumni = Alumni::findOrFail($data['alumni_id']);

       $this->validateEmploymentDates($data);

       $attributes = [
           'alumni_id' => $data['alumni_id'],
           'company_name' => trim($data['company_name']),
           'position' => trim($data['position']),
           'start_date' => $data['start_date'],
           'end_date' => $data['end_date'] ?? null,
           'is_current' => filter_var($data['is_current'], FILTER_VALIDATE_BOOLEAN),
           'responsibilities' => isset($data['responsibilities']) ? trim($data['responsibilities']) : null,
       ];

       $employmentHistory = EmploymentHistory::create($attributes);

       Log::info("Created employment history", ['id' => $employmentHistory->id, 'alumni_id' => $alumni->id]);

       return $employmentHistory;
   }

   public function delete(int $id) : void
   {
       $employmentHistory = EmploymentHistory::findOrFail($id);
       $employmentHistory->delete();

       Log::info("Deleted employment history", ['id' => $id]);
   }

   public function exists(int $id) : bool
   {
       return EmploymentHistory::where('id', $id)->exists();
   }

   public function getAll() : Collection
   {
       return EmploymentHistory::with('alumni')
           ->latest('start_date')
           ->get();
   }

   public function getByAlumni(int $alumniId) : Collection
   {
       return EmploymentHistory::where('alumni_id', $alumniId)
           ->orderByDesc('start_date')
           ->get();
   }

   public function getById(int $id) : EmploymentHistory
   {
       return EmploymentHistory::findOrFail($id);
   }

   public function update(int $id, array $data) : EmploymentHistory
   {
       $this->validateEmploymentDates($data);

       $employmentHistory = EmploymentHistory::findOrFail($id);

       if (!empty($data['alumni_id']) && $data['alumni_id'] !== $employmentHistory->alumni_id) {
           throw new BusinessRuleException("Cannot reassign employment history to a different alumnus.");
       }

       $employmentHistory->fill([
           'company_name' => trim($data['company_name']),
           'position' => trim($data['position']),
           'start_date' => $data['start_date'],
           'end_date' => $data['end_date'] ?? null,
           'is_current' => filter_var($data['is_current'], FILTER_VALIDATE_BOOLEAN),
           'responsibilities' => isset($data['responsibilities']) ? trim($data['responsibilities']) : null,
       ]);

       $employmentHistory->save();

       Log::info("Updated employment history", ['id' => $employmentHistory->id]);

       return $employmentHistory;
   }
}
