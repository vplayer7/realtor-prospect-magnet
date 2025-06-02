
import React, { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ChevronLeft, ChevronRight, User, Mail, Phone, Shield } from 'lucide-react';
import { FormData } from '@/pages/Index';

interface LeadCaptureFormProps {
  onNext: () => void;
  onPrev: () => void;
  onDataUpdate: (data: Partial<FormData>) => void;
  initialData: FormData;
}

const LeadCaptureForm: React.FC<LeadCaptureFormProps> = ({ onNext, onPrev, onDataUpdate, initialData }) => {
  const [formData, setFormData] = useState({
    name: initialData.name,
    email: initialData.email,
    phone: initialData.phone
  });

  const [errors, setErrors] = useState({
    name: '',
    email: '',
    phone: ''
  });

  const validateForm = () => {
    const newErrors = { name: '', email: '', phone: '' };
    let isValid = true;

    if (!formData.name.trim()) {
      newErrors.name = 'Name is required';
      isValid = false;
    }

    if (!formData.email.trim()) {
      newErrors.email = 'Email is required';
      isValid = false;
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = 'Please enter a valid email';
      isValid = false;
    }

    if (!formData.phone.trim()) {
      newErrors.phone = 'Phone number is required';
      isValid = false;
    }

    setErrors(newErrors);
    return isValid;
  };

  const handleInputChange = (field: string, value: string) => {
    const newFormData = { ...formData, [field]: value };
    setFormData(newFormData);
    onDataUpdate(newFormData);
    
    // Clear error when user starts typing
    if (errors[field as keyof typeof errors]) {
      setErrors(prev => ({ ...prev, [field]: '' }));
    }
  };

  const handleNext = () => {
    if (validateForm()) {
      onNext();
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center p-4">
      <Card className="w-full max-w-lg shadow-2xl">
        <CardHeader className="text-center pb-6">
          <div className="flex items-center justify-center mb-4">
            <div className="bg-green-100 p-3 rounded-full">
              <User className="w-8 h-8 text-green-600" />
            </div>
          </div>
          <CardTitle className="text-2xl font-bold text-gray-900 mb-2">
            Almost There!
          </CardTitle>
          <p className="text-gray-600">
            Let us know how to reach you with the perfect property matches
          </p>
        </CardHeader>
        
        <CardContent className="px-8 pb-8">
          <div className="space-y-6">
            <div className="space-y-2">
              <Label htmlFor="name" className="text-sm font-medium text-gray-700">
                Full Name *
              </Label>
              <div className="relative">
                <User className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
                <Input
                  id="name"
                  type="text"
                  placeholder="Enter your full name"
                  value={formData.name}
                  onChange={(e) => handleInputChange('name', e.target.value)}
                  className={`pl-12 h-12 ${errors.name ? 'border-red-500' : 'border-gray-200'} focus:border-green-500 rounded-xl`}
                />
              </div>
              {errors.name && <p className="text-red-500 text-sm">{errors.name}</p>}
            </div>

            <div className="space-y-2">
              <Label htmlFor="email" className="text-sm font-medium text-gray-700">
                Email Address *
              </Label>
              <div className="relative">
                <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
                <Input
                  id="email"
                  type="email"
                  placeholder="Enter your email address"
                  value={formData.email}
                  onChange={(e) => handleInputChange('email', e.target.value)}
                  className={`pl-12 h-12 ${errors.email ? 'border-red-500' : 'border-gray-200'} focus:border-green-500 rounded-xl`}
                />
              </div>
              {errors.email && <p className="text-red-500 text-sm">{errors.email}</p>}
            </div>

            <div className="space-y-2">
              <Label htmlFor="phone" className="text-sm font-medium text-gray-700">
                Phone Number *
              </Label>
              <div className="relative">
                <Phone className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
                <Input
                  id="phone"
                  type="tel"
                  placeholder="Enter your phone number"
                  value={formData.phone}
                  onChange={(e) => handleInputChange('phone', e.target.value)}
                  className={`pl-12 h-12 ${errors.phone ? 'border-red-500' : 'border-gray-200'} focus:border-green-500 rounded-xl`}
                />
              </div>
              {errors.phone && <p className="text-red-500 text-sm">{errors.phone}</p>}
            </div>

            <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <div className="flex items-start gap-3">
                <Shield className="w-5 h-5 text-blue-600 mt-0.5" />
                <div>
                  <h4 className="font-semibold text-blue-800 text-sm">Your Privacy Matters</h4>
                  <p className="text-blue-700 text-xs mt-1">
                    We'll only use your information to send you relevant property listings and market updates. 
                    You can unsubscribe at any time.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div className="flex justify-between mt-8">
            <Button 
              variant="outline" 
              onClick={onPrev}
              className="flex items-center gap-2"
            >
              <ChevronLeft className="w-4 h-4" />
              Back
            </Button>
            
            <Button 
              onClick={handleNext}
              className="flex items-center gap-2 bg-green-600 hover:bg-green-700"
            >
              View Results
              <ChevronRight className="w-4 h-4" />
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default LeadCaptureForm;
