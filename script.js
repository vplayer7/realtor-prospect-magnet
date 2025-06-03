// Global variables
let currentStep = 1;
let currentQuestion = 0;
let map = null;
let autocomplete = null;
let formData = {
    address: '',
    coordinates: null,
    name: '',
    email: '',
    phone: '',
    propertyType: '',
    bedrooms: '',
    bathrooms: '',
    priceRange: '',
    timeline: '',
    financing: ''
};

// Quiz questions data
const quizQuestions = [
    {
        id: 'propertyType',
        title: 'What type of property are you looking for?',
        icon: 'fas fa-home',
        options: [
            { value: 'single-family', label: 'Single Family Home' },
            { value: 'condo', label: 'Condominium' },
            { value: 'townhouse', label: 'Townhouse' },
            { value: 'multi-family', label: 'Multi-Family' }
        ]
    },
    {
        id: 'bedrooms',
        title: 'How many bedrooms do you need?',
        icon: 'fas fa-bed',
        options: [
            { value: '1', label: '1 Bedroom' },
            { value: '2', label: '2 Bedrooms' },
            { value: '3', label: '3 Bedrooms' },
            { value: '4+', label: '4+ Bedrooms' }
        ]
    },
    {
        id: 'bathrooms',
        title: 'How many bathrooms do you prefer?',
        icon: 'fas fa-bath',
        options: [
            { value: '1', label: '1 Bathroom' },
            { value: '1.5', label: '1.5 Bathrooms' },
            { value: '2', label: '2 Bathrooms' },
            { value: '3+', label: '3+ Bathrooms' }
        ]
    },
    {
        id: 'priceRange',
        title: 'What is your budget range?',
        icon: 'fas fa-dollar-sign',
        options: [
            { value: 'under-300k', label: 'Under $300,000' },
            { value: '300k-500k', label: '$300,000 - $500,000' },
            { value: '500k-750k', label: '$500,000 - $750,000' },
            { value: 'over-750k', label: 'Over $750,000' }
        ]
    },
    {
        id: 'timeline',
        title: 'When are you looking to buy?',
        icon: 'fas fa-calendar',
        options: [
            { value: 'immediately', label: 'Immediately' },
            { value: '1-3-months', label: '1-3 Months' },
            { value: '3-6-months', label: '3-6 Months' },
            { value: '6-months-plus', label: '6+ Months' }
        ]
    },
    {
        id: 'financing',
        title: 'How will you finance your purchase?',
        icon: 'fas fa-credit-card',
        options: [
            { value: 'mortgage', label: 'Mortgage/Loan' },
            { value: 'cash', label: 'Cash Purchase' },
            { value: 'pre-approved', label: 'Pre-approved' },
            { value: 'need-help', label: 'Need Help with Financing' }
        ]
    }
];

// Initialize the application
function initializeApp() {
    initializeAddressSearch();
    setupEventListeners();
    showStep(1);
}

// Initialize Google Maps autocomplete
function initializeAddressSearch() {
    const addressInput = document.getElementById('addressInput');
    
    if (window.google && addressInput) {
        autocomplete = new google.maps.places.Autocomplete(addressInput, {
            types: ['address'],
            componentRestrictions: { country: 'us' }
        });

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (place.geometry) {
                formData.address = place.formatted_address;
                formData.coordinates = {
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng()
                };
                enableStartButton();
            }
        });
    }
}

// Setup all event listeners
function setupEventListeners() {
    // Address input
    const addressInput = document.getElementById('addressInput');
    addressInput.addEventListener('input', function() {
        const startBtn = document.getElementById('startSearchBtn');
        if (this.value.trim()) {
            startBtn.disabled = false;
        } else {
            startBtn.disabled = true;
        }
    });

    // Start search button
    document.getElementById('startSearchBtn').addEventListener('click', function() {
        if (formData.address || document.getElementById('addressInput').value.trim()) {
            if (!formData.address) {
                formData.address = document.getElementById('addressInput').value.trim();
            }
            showStep(2);
            initializeQuiz();
        }
    });

    // Quiz navigation
    document.getElementById('quizBackBtn').addEventListener('click', function() {
        if (currentQuestion > 0) {
            currentQuestion--;
            displayQuestion();
        } else {
            showStep(1);
        }
    });

    document.getElementById('quizNextBtn').addEventListener('click', function() {
        if (currentQuestion < quizQuestions.length - 1) {
            currentQuestion++;
            displayQuestion();
        } else {
            showStep(3);
        }
    });

    // Form navigation
    document.getElementById('formBackBtn').addEventListener('click', function() {
        showStep(2);
    });

    document.getElementById('formNextBtn').addEventListener('click', function() {
        if (validateForm()) {
            showStep(4);
            initializeMap();
            displayPreferences();
        }
    });

    // Results navigation
    document.getElementById('resultsBackBtn').addEventListener('click', function() {
        showStep(3);
    });

    document.getElementById('submitBtn').addEventListener('click', function() {
        submitForm();
    });

    // Form inputs
    setupFormValidation();
}

// Enable start button
function enableStartButton() {
    document.getElementById('startSearchBtn').disabled = false;
}

// Show specific step
function showStep(stepNumber) {
    // Hide all steps
    const steps = document.querySelectorAll('.step');
    steps.forEach(step => {
        step.classList.remove('active');
    });

    // Show current step
    const currentStepElement = document.getElementById(`step${stepNumber}`);
    if (currentStepElement) {
        currentStepElement.classList.add('active');
        currentStepElement.classList.add('fade-in');
    }

    currentStep = stepNumber;
}

// Initialize quiz
function initializeQuiz() {
    currentQuestion = 0;
    displayQuestion();
}

// Display current quiz question
function displayQuestion() {
    const question = quizQuestions[currentQuestion];
    
    // Update question content
    document.getElementById('questionIcon').className = question.icon;
    document.getElementById('questionTitle').textContent = question.title;
    
    // Update progress
    const progress = ((currentQuestion + 1) / quizQuestions.length) * 100;
    document.getElementById('progressFill').style.width = `${progress}%`;
    document.getElementById('progressText').textContent = `Question ${currentQuestion + 1} of ${quizQuestions.length}`;
    
    // Create options
    const optionsContainer = document.getElementById('questionOptions');
    optionsContainer.innerHTML = '';
    
    question.options.forEach(option => {
        const optionElement = document.createElement('div');
        optionElement.className = 'option';
        optionElement.innerHTML = `
            <input type="radio" name="question${currentQuestion}" value="${option.value}" id="${option.value}">
            <label for="${option.value}">${option.label}</label>
        `;
        
        optionElement.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            updateQuestionSelection();
        });
        
        optionsContainer.appendChild(optionElement);
    });
    
    // Check if we have a previous answer
    const previousAnswer = formData[question.id];
    if (previousAnswer) {
        const radio = document.querySelector(`input[value="${previousAnswer}"]`);
        if (radio) {
            radio.checked = true;
            updateQuestionSelection();
        }
    } else {
        document.getElementById('quizNextBtn').disabled = true;
    }
    
    // Update back button text
    const backBtn = document.getElementById('quizBackBtn');
    if (currentQuestion === 0) {
        backBtn.innerHTML = '<i class="fas fa-chevron-left"></i> Back';
    } else {
        backBtn.innerHTML = '<i class="fas fa-chevron-left"></i> Back';
    }
    
    // Update next button text
    const nextBtn = document.getElementById('quizNextBtn');
    if (currentQuestion === quizQuestions.length - 1) {
        nextBtn.innerHTML = 'Continue <i class="fas fa-chevron-right"></i>';
    } else {
        nextBtn.innerHTML = 'Next <i class="fas fa-chevron-right"></i>';
    }
}

// Update quiz selection
function updateQuestionSelection() {
    const question = quizQuestions[currentQuestion];
    const selectedOption = document.querySelector(`input[name="question${currentQuestion}"]:checked`);
    
    if (selectedOption) {
        formData[question.id] = selectedOption.value;
        document.getElementById('quizNextBtn').disabled = false;
        
        // Update visual selection
        document.querySelectorAll('.option').forEach(option => {
            option.classList.remove('selected');
        });
        selectedOption.closest('.option').classList.add('selected');
    }
}

// Setup form validation
function setupFormValidation() {
    const inputs = ['fullName', 'email', 'phone'];
    
    inputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        input.addEventListener('input', function() {
            clearError(inputId);
            updateFormData();
        });
        
        input.addEventListener('blur', function() {
            validateField(inputId);
        });
    });
}

// Update form data
function updateFormData() {
    formData.name = document.getElementById('fullName').value;
    formData.email = document.getElementById('email').value;
    formData.phone = document.getElementById('phone').value;
}

// Validate individual field
function validateField(fieldId) {
    const input = document.getElementById(fieldId);
    const value = input.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    switch (fieldId) {
        case 'fullName':
            if (!value) {
                errorMessage = 'Name is required';
                isValid = false;
            }
            break;
        case 'email':
            if (!value) {
                errorMessage = 'Email is required';
                isValid = false;
            } else if (!/\S+@\S+\.\S+/.test(value)) {
                errorMessage = 'Please enter a valid email';
                isValid = false;
            }
            break;
        case 'phone':
            if (!value) {
                errorMessage = 'Phone number is required';
                isValid = false;
            }
            break;
    }
    
    if (!isValid) {
        showError(fieldId, errorMessage);
    }
    
    return isValid;
}

// Validate entire form
function validateForm() {
    updateFormData();
    
    const fields = ['fullName', 'email', 'phone'];
    let isValid = true;
    
    fields.forEach(fieldId => {
        if (!validateField(fieldId)) {
            isValid = false;
        }
    });
    
    return isValid;
}

// Show field error
function showError(fieldId, message) {
    const input = document.getElementById(fieldId);
    const errorElement = document.getElementById(fieldId.replace('full', '').toLowerCase() + 'Error');
    
    input.classList.add('error');
    if (errorElement) {
        errorElement.textContent = message;
    }
}

// Clear field error
function clearError(fieldId) {
    const input = document.getElementById(fieldId);
    const errorElement = document.getElementById(fieldId.replace('full', '').toLowerCase() + 'Error');
    
    input.classList.remove('error');
    if (errorElement) {
        errorElement.textContent = '';
    }
}

// Initialize map on results page
function initializeMap() {
    if (!window.google || !formData.coordinates) return;
    
    const mapElement = document.getElementById('map');
    if (!mapElement) return;
    
    map = new google.maps.Map(mapElement, {
        center: formData.coordinates,
        zoom: 15,
        styles: [
            {
                featureType: "poi",
                elementType: "labels",
                stylers: [{ visibility: "off" }]
            }
        ]
    });
    
    // Add marker
    new google.maps.Marker({
        position: formData.coordinates,
        map: map,
        title: formData.address,
        icon: {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="20" cy="20" r="18" fill="#2563eb" stroke="white" stroke-width="4"/>
                    <path d="M20 10L20 30M10 20L30 20" stroke="white" stroke-width="3" stroke-linecap="round"/>
                </svg>
            `),
            scaledSize: new google.maps.Size(40, 40),
            anchor: new google.maps.Point(20, 20)
        }
    });
}

// Display user preferences summary
function displayPreferences() {
    document.getElementById('selectedAddress').textContent = formData.address;
    
    const summaryContainer = document.getElementById('preferencesSummary');
    summaryContainer.innerHTML = '';
    
    const preferences = [
        { label: 'Property Type', value: getPropertyTypeLabel(formData.propertyType) },
        { label: 'Bedrooms', value: formData.bedrooms },
        { label: 'Bathrooms', value: formData.bathrooms },
        { label: 'Price Range', value: getPriceRangeLabel(formData.priceRange) },
        { label: 'Timeline', value: getTimelineLabel(formData.timeline) },
        { label: 'Financing', value: getFinancingLabel(formData.financing) }
    ];
    
    preferences.forEach(pref => {
        if (pref.value) {
            const prefElement = document.createElement('div');
            prefElement.className = 'preference-item';
            prefElement.innerHTML = `
                <span class="preference-label">${pref.label}:</span>
                <span class="preference-value">${pref.value}</span>
            `;
            summaryContainer.appendChild(prefElement);
        }
    });
}

// Label helper functions
function getPropertyTypeLabel(type) {
    const types = {
        'single-family': 'Single Family Home',
        'condo': 'Condominium',
        'townhouse': 'Townhouse',
        'multi-family': 'Multi-Family'
    };
    return types[type] || type;
}

function getPriceRangeLabel(range) {
    const ranges = {
        'under-300k': 'Under $300,000',
        '300k-500k': '$300,000 - $500,000',
        '500k-750k': '$500,000 - $750,000',
        'over-750k': 'Over $750,000'
    };
    return ranges[range] || range;
}

function getTimelineLabel(timeline) {
    const timelines = {
        'immediately': 'Immediately',
        '1-3-months': '1-3 Months',
        '3-6-months': '3-6 Months',
        '6-months-plus': '6+ Months'
    };
    return timelines[timeline] || timeline;
}

function getFinancingLabel(financing) {
    const options = {
        'mortgage': 'Mortgage/Loan',
        'cash': 'Cash Purchase',
        'pre-approved': 'Pre-approved',
        'need-help': 'Need Help with Financing'
    };
    return options[financing] || financing;
}

// Submit form
function submitForm() {
    console.log('Final form data:', formData);
    
    // Here you would normally send data to your backend
    // For demo purposes, we'll show the success modal
    
    document.getElementById('submittedName').textContent = formData.name;
    document.getElementById('successModal').classList.add('show');
    
    // You can add AJAX call here to send data to your PHP backend
    // Example:
    // fetch('submit.php', {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/json',
    //     },
    //     body: JSON.stringify(formData)
    // })
    // .then(response => response.json())
    // .then(data => {
    //     console.log('Success:', data);
    // })
    // .catch((error) => {
    //     console.error('Error:', error);
    // });
}

// Start new search
function startNewSearch() {
    // Reset form data
    formData = {
        address: '',
        coordinates: null,
        name: '',
        email: '',
        phone: '',
        propertyType: '',
        bedrooms: '',
        bathrooms: '',
        priceRange: '',
        timeline: '',
        financing: ''
    };
    
    // Reset quiz
    currentQuestion = 0;
    
    // Clear form inputs
    document.getElementById('addressInput').value = '';
    document.getElementById('fullName').value = '';
    document.getElementById('email').value = '';
    document.getElementById('phone').value = '';
    
    // Hide modal and show first step
    document.getElementById('successModal').classList.remove('show');
    showStep(1);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // If Google Maps is already loaded, initialize immediately
    if (window.google) {
        initializeApp();
    }
    // Otherwise, wait for the callback from the Google Maps script
});

// Global callback for Google Maps
window.initializeApp = initializeApp;
