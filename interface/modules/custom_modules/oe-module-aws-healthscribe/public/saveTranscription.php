<?php
function saveTranscriptionToDatabase($transcription, $patientId, $encounterId, $dbConnection) {
    $stmt = $dbConnection->prepare("
        INSERT INTO transcription_records (patient_id, encounter_id, transcription, created_at)
        VALUES (:patient_id, :encounter_id, :transcription, NOW())
    ");
    $stmt->execute([
        ':patient_id' => $patientId,
        ':encounter_id' => $encounterId,
        ':transcription' => $transcription,
    ]);
}
