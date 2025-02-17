@component('mail::message')
# New Tournament Registration

A new team has registered for your tournament:

**Tournament:** {{ $registration->tournament->name }}  
**Team:** {{ $registration->team->name }}  
**Registration Date:** {{ $registration->created_at->format('M d, Y H:i') }}

@component('mail::button', ['url' => $url])
View Registration
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent