<?php

namespace App\Imports;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

// class EventsImport implements ToModel, SkipsEmptyRows, WithValidation, WithHeadingRow, SkipsOnFailure
class EventsImport implements ToModel, SkipsEmptyRows, WithHeadingRow, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        // dd($row);

        return new Event([
            'user_id' => Auth::user()->id,

            'name' => isset($row['event_name']) ? $row['event_name'] : null,

            'description' => isset($row['event_description']) ? $row['event_description'] : null,

            'date' => isset($row['event_date']) ? $row['event_date'] : null,

            'number_of_attendees' => isset($row['number_of_attendees']) ? $row['number_of_attendees'] : null,

            'unit_of_attendees' => isset($row['unit_of_attendees']) ? $row['unit_of_attendees'] : null,

            'dimension' => isset($row['dimension']) ? $row['dimension'] : null,

            'type' => isset($row['type']) ? $row['type'] : null,

            'whether_virtual' => isset($row['whether_virtual']) ? $row['whether_virtual'] : null,

            'languages' => isset($row['languages_used']) ? $row['languages_used'] : null,

            'is_internal' => isset($row['internal_or_external']) ? $row['internal_or_external'] : null,

            'any_partners' => isset($row['any_co_hostingplanning_community_partners']) ? $row['any_co_hostingplanning_community_partners'] : null,

            'component_covid19' => isset($row['any_component_responding_to_or_recovery_covid19']) ? $row['any_component_responding_to_or_recovery_covid19'] : null,

            'component_addressing' => isset($row['any_component_on_addressing_structural_racism_anti_racism_racial_justice']) ? $row['any_component_on_addressing_structural_racism_anti_racism_racial_justice'] : null,

            'leadership_level' => isset($row['community_engagementleadership_level']) ? $row['community_engagementleadership_level'] : null,

            'resources' => isset($row['support_or_resources_shared_with_the_participants_during_the_event']) ? $row['support_or_resources_shared_with_the_participants_during_the_event'] : null,

            'optional_1' => isset($row['optional_1_any_tool_or_resource_you_used_that_helped_plan_this_event']) ? $row['optional_1_any_tool_or_resource_you_used_that_helped_plan_this_event'] : null,

            'optional_2' => isset($row['optional_2_any_resources_needed_in_the_future']) ? $row['optional_2_any_resources_needed_in_the_future'] : null,

            'optional_3' => isset($row['optional_3_stories_from_community_to_share']) ? $row['optional_3_stories_from_community_to_share'] : null,

        ]);
    }

    public function rules(): array
    {
        return [
            'event_name' => [
                'required',
                'string',
            ],
        ];
    }

    public function onFailure(Failure...$failures)
    {
        // it skips error.
        return response($failures);
    }
}
