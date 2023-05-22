<x-mail::message>
# Resetare parolă

Bună, {{$user->name}},<br/>
Parola ta a fost resetată. Noua parolă este: {{$initialPassword}}.<br/>
Te rugăm să setezi o nouă parolă imediat după logare.

O zi frumoasă,<br>
Echipa {{ config('app.name') }}
</x-mail::message>
