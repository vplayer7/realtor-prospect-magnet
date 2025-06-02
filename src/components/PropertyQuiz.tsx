
import React, { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { ChevronLeft, ChevronRight, Home, Bed, Bath, DollarSign, Calendar, CreditCard } from 'lucide-react';
import { FormData } from '@/pages/Index';

interface PropertyQuizProps {
  onNext: () => void;
  onPrev: () => void;
  onDataUpdate: (data: Partial<FormData>) => void;
  initialData: FormData;
}

const PropertyQuiz: React.FC<PropertyQuizProps> = ({ onNext, onPrev, onDataUpdate, initialData }) => {
  const [currentQuestion, setCurrentQuestion] = useState(0);
  const [answers, setAnswers] = useState({
    propertyType: initialData.propertyType,
    bedrooms: initialData.bedrooms,
    bathrooms: initialData.bathrooms,
    priceRange: initialData.priceRange,
    timeline: initialData.timeline,
    financing: initialData.financing
  });

  const questions = [
    {
      id: 'propertyType',
      title: 'What type of property are you looking for?',
      icon: <Home className="w-6 h-6" />,
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
      icon: <Bed className="w-6 h-6" />,
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
      icon: <Bath className="w-6 h-6" />,
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
      icon: <DollarSign className="w-6 h-6" />,
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
      icon: <Calendar className="w-6 h-6" />,
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
      icon: <CreditCard className="w-6 h-6" />,
      options: [
        { value: 'mortgage', label: 'Mortgage/Loan' },
        { value: 'cash', label: 'Cash Purchase' },
        { value: 'pre-approved', label: 'Pre-approved' },
        { value: 'need-help', label: 'Need Help with Financing' }
      ]
    }
  ];

  const currentQuestionData = questions[currentQuestion];

  const handleAnswerChange = (value: string) => {
    const newAnswers = { ...answers, [currentQuestionData.id]: value };
    setAnswers(newAnswers);
    onDataUpdate(newAnswers);
  };

  const handleNext = () => {
    if (currentQuestion < questions.length - 1) {
      setCurrentQuestion(prev => prev + 1);
    } else {
      onNext();
    }
  };

  const handlePrev = () => {
    if (currentQuestion > 0) {
      setCurrentQuestion(prev => prev - 1);
    } else {
      onPrev();
    }
  };

  const currentAnswer = answers[currentQuestionData.id as keyof typeof answers];
  const progress = ((currentQuestion + 1) / questions.length) * 100;

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
      <Card className="w-full max-w-2xl shadow-2xl">
        <CardHeader className="text-center pb-4">
          <div className="flex items-center justify-center mb-4">
            {currentQuestionData.icon}
          </div>
          <CardTitle className="text-2xl font-bold text-gray-900">
            {currentQuestionData.title}
          </CardTitle>
          <div className="mt-4">
            <div className="w-full bg-gray-200 rounded-full h-2">
              <div 
                className="bg-blue-600 h-2 rounded-full transition-all duration-300"
                style={{ width: `${progress}%` }}
              ></div>
            </div>
            <p className="text-sm text-gray-500 mt-2">
              Question {currentQuestion + 1} of {questions.length}
            </p>
          </div>
        </CardHeader>
        
        <CardContent className="px-8 pb-8">
          <RadioGroup 
            value={currentAnswer} 
            onValueChange={handleAnswerChange}
            className="space-y-4"
          >
            {currentQuestionData.options.map((option) => (
              <div key={option.value} className="flex items-center space-x-3 p-4 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors cursor-pointer">
                <RadioGroupItem value={option.value} id={option.value} />
                <Label htmlFor={option.value} className="flex-1 cursor-pointer font-medium">
                  {option.label}
                </Label>
              </div>
            ))}
          </RadioGroup>

          <div className="flex justify-between mt-8">
            <Button 
              variant="outline" 
              onClick={handlePrev}
              className="flex items-center gap-2"
            >
              <ChevronLeft className="w-4 h-4" />
              Back
            </Button>
            
            <Button 
              onClick={handleNext}
              disabled={!currentAnswer}
              className="flex items-center gap-2 bg-blue-600 hover:bg-blue-700"
            >
              {currentQuestion < questions.length - 1 ? 'Next' : 'Continue'}
              <ChevronRight className="w-4 h-4" />
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default PropertyQuiz;
