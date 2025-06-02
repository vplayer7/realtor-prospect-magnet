
import React, { useEffect, useRef, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ChevronLeft, MapPin, Home, CheckCircle, Mail, Phone } from 'lucide-react';
import { FormData } from '@/pages/Index';

interface PropertyMapProps {
  onPrev: () => void;
  onSubmit: () => void;
  formData: FormData;
}

declare global {
  interface Window {
    google: any;
  }
}

const PropertyMap: React.FC<PropertyMapProps> = ({ onPrev, onSubmit, formData }) => {
  const mapRef = useRef<HTMLDivElement>(null);
  const [map, setMap] = useState<any>(null);
  const [isSubmitted, setIsSubmitted] = useState(false);

  useEffect(() => {
    if (window.google && mapRef.current && formData.coordinates) {
      initializeMap();
    }
  }, [formData.coordinates]);

  const initializeMap = () => {
    if (!formData.coordinates || !mapRef.current) return;

    const mapInstance = new window.google.maps.Map(mapRef.current, {
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

    // Add marker for the searched location
    new window.google.maps.Marker({
      position: formData.coordinates,
      map: mapInstance,
      title: formData.address,
      icon: {
        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
          <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="20" cy="20" r="18" fill="#3B82F6" stroke="white" stroke-width="4"/>
            <path d="M20 10L20 30M10 20L30 20" stroke="white" stroke-width="3" stroke-linecap="round"/>
          </svg>
        `),
        scaledSize: new window.google.maps.Size(40, 40),
        anchor: new window.google.maps.Point(20, 20)
      }
    });

    setMap(mapInstance);
  };

  const handleSubmit = () => {
    setIsSubmitted(true);
    onSubmit();
  };

  const getPropertyTypeLabel = (type: string) => {
    const types: { [key: string]: string } = {
      'single-family': 'Single Family Home',
      'condo': 'Condominium',
      'townhouse': 'Townhouse',
      'multi-family': 'Multi-Family'
    };
    return types[type] || type;
  };

  const getPriceRangeLabel = (range: string) => {
    const ranges: { [key: string]: string } = {
      'under-300k': 'Under $300,000',
      '300k-500k': '$300,000 - $500,000',
      '500k-750k': '$500,000 - $750,000',
      'over-750k': 'Over $750,000'
    };
    return ranges[range] || range;
  };

  if (isSubmitted) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center p-4">
        <Card className="w-full max-w-lg shadow-2xl text-center">
          <CardHeader className="pb-6">
            <div className="flex items-center justify-center mb-4">
              <div className="bg-green-100 p-4 rounded-full">
                <CheckCircle className="w-12 h-12 text-green-600" />
              </div>
            </div>
            <CardTitle className="text-2xl font-bold text-gray-900 mb-2">
              Thank You, {formData.name}!
            </CardTitle>
            <p className="text-gray-600">
              Your property search preferences have been submitted successfully.
            </p>
          </CardHeader>
          
          <CardContent className="px-8 pb-8">
            <div className="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
              <h3 className="font-semibold text-blue-800 mb-3">What happens next?</h3>
              <div className="space-y-2 text-sm text-blue-700">
                <div className="flex items-start gap-2">
                  <Mail className="w-4 h-4 mt-0.5" />
                  <span>You'll receive matching properties via email within 24 hours</span>
                </div>
                <div className="flex items-start gap-2">
                  <Phone className="w-4 h-4 mt-0.5" />
                  <span>Our expert agent will call you to discuss your needs</span>
                </div>
                <div className="flex items-start gap-2">
                  <Home className="w-4 h-4 mt-0.5" />
                  <span>We'll schedule property viewings that match your criteria</span>
                </div>
              </div>
            </div>
            
            <Button 
              onClick={() => window.location.reload()}
              className="w-full bg-blue-600 hover:bg-blue-700"
            >
              Start Another Search
            </Button>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 p-4">
      <div className="max-w-6xl mx-auto">
        <Card className="shadow-2xl overflow-hidden">
          <CardHeader className="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
            <CardTitle className="text-2xl font-bold flex items-center gap-2">
              <MapPin className="w-6 h-6" />
              Your Property Search Summary
            </CardTitle>
            <p className="text-blue-100">
              Review your preferences and explore the area
            </p>
          </CardHeader>
          
          <CardContent className="p-0">
            <div className="grid md:grid-cols-2 gap-0">
              {/* Summary Panel */}
              <div className="p-8 bg-white">
                <h3 className="text-xl font-semibold mb-6 text-gray-900">Search Details</h3>
                
                <div className="space-y-4">
                  <div className="flex justify-between items-center py-2 border-b border-gray-100">
                    <span className="text-gray-600">Location:</span>
                    <span className="font-medium text-gray-900">{formData.address}</span>
                  </div>
                  
                  <div className="flex justify-between items-center py-2 border-b border-gray-100">
                    <span className="text-gray-600">Property Type:</span>
                    <span className="font-medium text-gray-900">{getPropertyTypeLabel(formData.propertyType)}</span>
                  </div>
                  
                  <div className="flex justify-between items-center py-2 border-b border-gray-100">
                    <span className="text-gray-600">Bedrooms:</span>
                    <span className="font-medium text-gray-900">{formData.bedrooms}</span>
                  </div>
                  
                  <div className="flex justify-between items-center py-2 border-b border-gray-100">
                    <span className="text-gray-600">Bathrooms:</span>
                    <span className="font-medium text-gray-900">{formData.bathrooms}</span>
                  </div>
                  
                  <div className="flex justify-between items-center py-2 border-b border-gray-100">
                    <span className="text-gray-600">Budget:</span>
                    <span className="font-medium text-gray-900">{getPriceRangeLabel(formData.priceRange)}</span>
                  </div>
                  
                  <div className="flex justify-between items-center py-2 border-b border-gray-100">
                    <span className="text-gray-600">Timeline:</span>
                    <span className="font-medium text-gray-900">{formData.timeline.replace('-', ' ')}</span>
                  </div>
                </div>

                <div className="mt-8 p-4 bg-gray-50 rounded-lg">
                  <h4 className="font-semibold text-gray-900 mb-2">Contact Information</h4>
                  <div className="space-y-1 text-sm text-gray-600">
                    <p><strong>Name:</strong> {formData.name}</p>
                    <p><strong>Email:</strong> {formData.email}</p>
                    <p><strong>Phone:</strong> {formData.phone}</p>
                  </div>
                </div>

                <div className="flex gap-3 mt-8">
                  <Button 
                    variant="outline" 
                    onClick={onPrev}
                    className="flex items-center gap-2"
                  >
                    <ChevronLeft className="w-4 h-4" />
                    Back
                  </Button>
                  
                  <Button 
                    onClick={handleSubmit}
                    className="flex-1 bg-green-600 hover:bg-green-700"
                  >
                    Submit & Get Matches
                  </Button>
                </div>
              </div>

              {/* Map Panel */}
              <div className="relative bg-gray-200">
                <div 
                  ref={mapRef}
                  className="w-full h-full min-h-[500px]"
                />
                {!formData.coordinates && (
                  <div className="absolute inset-0 flex items-center justify-center bg-gray-100">
                    <p className="text-gray-500">Map will appear here once location is set</p>
                  </div>
                )}
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default PropertyMap;
