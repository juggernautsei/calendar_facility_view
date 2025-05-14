/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

// App.jsx
import React from 'react';
import GreetingAgent from './components/greetings/GreetingAgent';
import ArvaisAI from './components/ArvaisAI';

const App = () => {
  return (
    <div className="app-container">
      <h1>Wound Care Assistant</h1>
      <ArvaisAI />
      <GreetingAgent />
    </div>
  );
};

export default App;
