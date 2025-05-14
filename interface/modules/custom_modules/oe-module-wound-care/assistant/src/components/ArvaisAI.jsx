/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

import React, { useState, useEffect } from "react";
import axios from "axios";

const ArvaisAI = () => {
    const [step, setStep] = useState(0);
    const [responses, setResponses] = useState({});
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    // Conversation steps
    const conversationFlow = [
        { text: "Are you ready to share information about the patient visit today?", field: "ready", options: ["Yes", "No"] },
        { text: "What brings the patient in today?", field: "visit_reason", options: ["New Wound", "Follow-up", "Post-procedure", "Other"] },
        { text: "Is this visit related to any underlying condition?", field: "related_condition", options: ["Diabetes", "Pressure Ulcer", "Surgical Wound", "No"] }
    ];

    useEffect(() => {
        if (step >= conversationFlow.length) {
            submitResponses();
        }
    }, [step]);

    const handleResponse = async (response) => {
        const field = conversationFlow[step].field;
        setResponses(prev => ({ ...prev, [field]: response }));

        if (step + 1 < conversationFlow.length) {
            setStep(step + 1);
        }
    };

    const submitResponses = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await axios.post("/visit-intake/complete", {
                pid: 123, user_id: 5, ...responses
            });

            alert("Visit Intake Completed Successfully");
        } catch (err) {
            setError(err.message || "Failed to submit responses");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="chat-container">
            {loading ? <p>Submitting...</p> :
                step < conversationFlow.length ? (
                    <div>
                        <p>{conversationFlow[step].text}</p>
                        {conversationFlow[step].options.map((option) => (
                            <button key={option} onClick={() => handleResponse(option)}>{option}</button>
                        ))}
                    </div>
                ) : <p>Thank you! Processing your responses...</p>
            }
            {error && <p className="error">{error}</p>}
        </div>
    );
};

export default ArvaisAI;
