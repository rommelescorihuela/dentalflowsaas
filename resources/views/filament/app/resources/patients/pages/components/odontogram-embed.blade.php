@livewire('odontogram', [
    'record' => $patient,
    'odontogramId' => $odontogramId
], key('odontogram-' . $odontogramId))
