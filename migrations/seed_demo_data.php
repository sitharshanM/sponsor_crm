<?php
// migrations/seed_demo_data.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/User.php';
require_once __DIR__ . '/../src/Sponsor.php';
require_once __DIR__ . '/../src/Interaction.php';

$userModel = new User($pdo);
$sponsorModel = new Sponsor($pdo);
$interactionModel = new Interaction($pdo);

// Create default admin user if not exists
$admin = $userModel->findByUsername('admin');
if (!$admin) {
    $userModel->create([
        'username' => 'admin',
        'email' => 'admin@sponsorcrm.local',
        'password' => 'admin123',
        'full_name' => 'Administrator'
    ]);
    echo "Default admin user created (username: admin, password: admin123)\n";
} else {
    echo "Admin user already exists.\n";
}

// Create demo sponsors if none exist
$existing = $sponsorModel->all();
if (empty($existing)) {
    $sponsors = [
        [
            'company_name' => 'TechCorp Inc.',
            'contact_person' => 'John Smith',
            'email' => 'john@techcorp.com',
            'phone' => '555-0101',
            'industry' => 'Technology',
            'sponsor_type' => 'Gold',
            'status' => 'interested'
        ],
        [
            'company_name' => 'Global Solutions Ltd.',
            'contact_person' => 'Sarah Johnson',
            'email' => 'sarah@globalsolutions.com',
            'phone' => '555-0102',
            'industry' => 'Consulting',
            'sponsor_type' => 'Silver',
            'status' => 'in_progress'
        ],
        [
            'company_name' => 'Innovation Partners',
            'contact_person' => 'Mike Davis',
            'email' => 'mike@innovation.com',
            'phone' => '555-0103',
            'industry' => 'Finance',
            'sponsor_type' => 'Bronze',
            'status' => 'new'
        ]
    ];

    foreach ($sponsors as $sponsorData) {
        $sponsorId = $sponsorModel->create($sponsorData);
        echo "Created sponsor: {$sponsorData['company_name']}\n";
        
        // Add a sample interaction
        $interactionModel->create([
            'sponsor_id' => $sponsorId,
            'interaction_type' => 'Initial Contact',
            'notes' => 'Initial outreach completed. Awaiting response.',
            'next_followup_date' => date('Y-m-d', strtotime('+7 days'))
        ]);
    }
    echo "Demo data seeded successfully.\n";
} else {
    echo "Sponsors already exist. Skipping demo data.\n";
}

