<?php

namespace App\Exports;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EventsExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Event::get()->makeHidden(['id', 'user_id', 'updated_at', 'created_at']);
    }

    public function headings(): array
    {
        return [
            'Event name',
            'Event description',
            'Event date',
            'Number of attendees',
            'Unit of attendees',
            'Dimension',
            'Type',
            'Whether virtual',
            'Languages used',
            'Internal or external',
            'Any co-hosting/planning community partners?',
            'Any component responding to (or recovery) COVID19?',
            'Any component on addressing structural racism, anti-racism, racial justice?',
            'Community engagement/leadership level',
            'Support or resources shared with the participants during the event',
            'Optional 1: any tool or resource you used that helped plan this event?',
            'Optional 2: any resources needed in the future?',
            'Optional 3: stories from community to share',
        ];
    }
}
