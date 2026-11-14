<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;

final class ProfileCompletionService
{
    /**
     * Calculate completeness of supplied profile data, not employability or AI fit.
     *
     * A saved student ID or credential URL does not imply institutional verification.
     *
     * @return array{
     *     percent: int,
     *     missing: list<array{complete: bool, points: int, label: string, route: string}>
     * }
     */
    public function calculate(User $user, ?Student $student, int $skillCount, bool $hasCredentialLink): array
    {
        $checks = [
            [
                'complete' => filled($user->first_name) && filled($user->last_name) && filled($user->email),
                'points' => 50,
                'label' => 'Complete personal details',
                'route' => 'portal.profile',
            ],
            [
                'complete' => $student && filled($student->program)
                    && trim($student->program) !== 'Not specified'
                    && $student->year_level && $student->gwa > 0,
                'points' => 20,
                'label' => 'Complete educational background',
                'route' => 'portal.profile',
            ],
            [
                'complete' => $student && filled($student->student_id_number),
                'points' => 10,
                'label' => 'Add student ID',
                'route' => 'portal.profile',
            ],
            [
                'complete' => $student && filled($student->resume_file_path),
                'points' => 10,
                'label' => 'Upload detailed resume',
                'route' => 'portal.resume',
            ],
            [
                'complete' => $skillCount > 0,
                'points' => 5,
                'label' => 'Add skills to your inventory',
                'route' => 'portal.skills',
            ],
            [
                'complete' => $hasCredentialLink,
                'points' => 5,
                'label' => 'Add a certification verification URL',
                'route' => 'portal.certifications',
            ],
        ];

        return [
            'percent' => collect($checks)->where('complete', true)->sum('points'),
            'missing' => array_values(array_filter($checks, fn ($check) => ! $check['complete'])),
        ];
    }
}
