CREATE TABLE `module_patient_app` (
  `id` int(11) NOT NULL,
  `fullname` varchar(75) NOT NULL,
  `email` varchar(75) NOT NULL,
  `remote_password` varchar(30) NOT NULL,
  `remote_id` int(3) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `identifier_data` int(11) DEFAULT NULL,
  `register_date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `module_patient_app`
--
ALTER TABLE `module_patient_app`
    ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `module_patient_app`
--
ALTER TABLE `module_patient_app`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
