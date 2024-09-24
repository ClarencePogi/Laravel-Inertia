<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;


class FeaturesController extends Controller
{
    public function index() {
        return Inertia::render('Features', [
                'future_featured' => [
                    'Trigonometric functions (sine, cosine, tangent).',
                    'Exponents and roots (squared, square root)',
                    'Logarithms (logarithm base 10, natural logarithm)',
                    'Use of buttons, sliders, or other UI elements to improve user experience.',
                    'Responsive design for mobile and desktop use.',
                    'History Log' => 'Keep a history of previous calculations for reference.',
                    'Keyboard Support' => 'Allow input via keyboard for better usability',
                    'Graphing Capabilities' => 'Plot mathematical functions on a graph.',
                    'Unit Conversions' => 'Convert between different units (length, weight, temperature, etc.).',
                    'Customizable Themes' => 'Allow users to switch between different themes (light/dark mode).',
                    'Error Handling' => 'Provide user-friendly error messages for invalid input or calculation errors.',
                    'Voice Input' => 'Implement voice recognition for input using speech-to-text technology.',
                    'Complex Number Support' => 'Handle complex numbers for advanced calculations.',
                    'API Integration' => 'Integrate with external APIs for additional features (e.g., currency conversion)',
                ],
        ]);
    }
}
