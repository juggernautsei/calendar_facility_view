/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

// GreetingAgent.js
import React, { useState } from 'react';

const GreetingAgent = () => {
    const [greetingMessage, setGreetingMessage] = useState('Hello! I\'m here to help with wound care documentation.');

    return (
        <div>
            <p>{greetingMessage}</p>
        </div>
    );
};

export default GreetingAgent;
