
import React, { useState, useRef, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { MapPin, Search } from 'lucide-react';
import { FormData } from '@/pages/Index';

interface AddressSearchProps {
  onNext: () => void;
  onDataUpdate: (data: Partial<FormData>) => void;
  initialData: FormData;
}

declare global {
  interface Window {
    google: any;
    initMap: () => void;
  }
}

const AddressSearch: React.FC<AddressSearchProps> = ({ onNext, onDataUpdate, initialData }) => {
  const [address, setAddress] = useState(initialData.address);
  const [suggestions, setSuggestions] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const autocompleteRef = useRef<any>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    // Load Google Maps API
    if (!window.google) {
      const script = document.createElement('script');
      script.src = `https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places&callback=initMap`;
      script.async = true;
      script.defer = true;
      
      window.initMap = () => {
        initializeAutocomplete();
      };
      
      document.head.appendChild(script);
    } else {
      initializeAutocomplete();
    }
  }, []);

  const initializeAutocomplete = () => {
    if (window.google && inputRef.current) {
      const autocomplete = new window.google.maps.places.Autocomplete(inputRef.current, {
        types: ['address'],
        componentRestrictions: { country: 'us' }
      });

      autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        if (place.geometry) {
          setAddress(place.formatted_address);
          onDataUpdate({
            address: place.formatted_address,
            coordinates: {
              lat: place.geometry.location.lat(),
              lng: place.geometry.location.lng()
            }
          });
        }
      });

      autocompleteRef.current = autocomplete;
    }
  };

  const handleNext = () => {
    if (address.trim()) {
      onNext();
    } else {
      alert('Please enter a valid address');
    }
  };

  return (
    <div 
      className="min-h-screen flex items-center justify-center relative bg-cover bg-center bg-no-repeat"
      style={{
        backgroundImage: `linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://images.unsplash.com/photo-1487958449943-2429e8be8625?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80')`
      }}
    >
      <div className="bg-white/95 backdrop-blur-sm rounded-2xl p-8 shadow-2xl max-w-md w-full mx-4">
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
            <MapPin className="w-8 h-8 text-blue-600" />
          </div>
          <h1 className="text-3xl font-bold text-gray-900 mb-2">
            Find Your Dream Home
          </h1>
          <p className="text-gray-600">
            Enter your desired location to get started
          </p>
        </div>

        <div className="space-y-6">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
            <Input
              ref={inputRef}
              type="text"
              placeholder="Enter address, city, or ZIP code"
              value={address}
              onChange={(e) => setAddress(e.target.value)}
              className="pl-12 h-12 text-lg border-2 border-gray-200 focus:border-blue-500 rounded-xl"
            />
          </div>

          <Button
            onClick={handleNext}
            className="w-full h-12 text-lg font-semibold bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors"
            disabled={!address.trim()}
          >
            Start Your Search
          </Button>
        </div>

        <div className="mt-8 text-center">
          <p className="text-sm text-gray-500">
            Powered by Google Maps • Trusted by thousands of buyers
          </p>
        </div>
      </div>
    </div>
  );
};

export default AddressSearch;
