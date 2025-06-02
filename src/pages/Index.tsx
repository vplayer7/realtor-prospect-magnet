
import React, { useState } from 'react';
import AddressSearch from '@/components/AddressSearch';
import PropertyQuiz from '@/components/PropertyQuiz';
import LeadCaptureForm from '@/components/LeadCaptureForm';
import PropertyMap from '@/components/PropertyMap';

export interface FormData {
  address: string;
  coordinates: { lat: number; lng: number } | null;
  name: string;
  email: string;
  phone: string;
  propertyType: string;
  bedrooms: string;
  bathrooms: string;
  priceRange: string;
  timeline: string;
  financing: string;
}

const Index = () => {
  const [currentStep, setCurrentStep] = useState(1);
  const [formData, setFormData] = useState<FormData>({
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
  });

  const nextStep = () => setCurrentStep(prev => prev + 1);
  const prevStep = () => setCurrentStep(prev => prev - 1);

  const updateFormData = (data: Partial<FormData>) => {
    setFormData(prev => ({ ...prev, ...data }));
  };

  const handleSubmit = async () => {
    console.log('Final form data:', formData);
    // Here you would normally send data to your backend
    alert('Thank you! Your information has been submitted.');
  };

  return (
    <div className="min-h-screen">
      {currentStep === 1 && (
        <AddressSearch
          onNext={nextStep}
          onDataUpdate={updateFormData}
          initialData={formData}
        />
      )}
      
      {currentStep === 2 && (
        <PropertyQuiz
          onNext={nextStep}
          onPrev={prevStep}
          onDataUpdate={updateFormData}
          initialData={formData}
        />
      )}
      
      {currentStep === 3 && (
        <LeadCaptureForm
          onNext={nextStep}
          onPrev={prevStep}
          onDataUpdate={updateFormData}
          initialData={formData}
        />
      )}
      
      {currentStep === 4 && (
        <PropertyMap
          onPrev={prevStep}
          onSubmit={handleSubmit}
          formData={formData}
        />
      )}
    </div>
  );
};

export default Index;
