<x-mail::message>
# Resetare parolă

Bună {{$user->name}},<br/>
Codul pentru resetarea parolei este: {{$resetPasswordCode}}.<br/>
Te rugăm să setezi o nouă parolă în aplicație.

O zi frumoasă,<br>
Echipa {{ config('app.name') }}
</x-mail::message>
