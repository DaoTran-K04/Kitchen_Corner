I. Subject Matter

1. Overview of the field and supporting cooking technologies:

• Analysis of the demand for home cooking and the trend of health and nutrition (Healthy Eating) in modern life.
• Research on existing food-related social media models (Cookpad, Tasty, Yummly).
• Overview of the application of technology in suggesting smart menus from available ingredients and automatically calculating nutrition.

2. System Analysis and Design:

• System Architecture: Built using the MVC model with 3 main components:
• Backend: Built using Laravel Framework (PHP) to manage user data, recipes, ingredients, and handle nutrition calculation logic.
• Frontend: Modern user interface (Responsive), optimized cooking experience (Cooking Mode).
• Admin Dashboard: Administration interface for reviewing posts, managing food categories, ingredients, and reporting violations.
• Database Design: Manages users, recipes, steps, ingredients, nutritional information, comments, favorites, and personal collections.

3. Integration of Artificial Intelligence (AI) and Smart Algorithms:

• Building an AI Chatbot (using Gemini API / OpenAI API) to:
• Advise on menus based on preferences and health status (e.g., weight loss menus, muscle gain menus).
• Answer questions about food preparation and preservation.
• Smart Search Algorithm:
• Users input a list of ingredients in their refrigerator -> The system suggests dishes that can be cooked.
• Nutrition Calculator:
• Automatically calculates the total calories, protein, carbohydrates, and fat of a dish based on the quantity of ingredients.

4. Application Development and Deployment:

• Building Business Flow 1 (Sharing & Interaction): Uploading recipes (Step-by-step) -> Reviewing posts -> Displaying details -> Users cook (Cooking Mode) -> Rating & Check-in results.
• Building Business Flow 2 (Supporting home cooks): Inputting available ingredients -> System filters suitable recipes -> Suggesting daily menus.
• Building a content-based recommendation system (Content-based Filtering): Analyzing users' cooking preferences to suggest new dishes that match their tastes.
• Setting up a notification system: Notifying users when someone comments on or likes their dish, or when there's a new dish from a chef they follow.

5. Conclusion & Future Development:

• Evaluating the accuracy of the nutritional calculation tool compared to reality.
• Researching the integration of AI (Image Recognition) for food image recognition to automatically suggest recipes. • Future expansion: Develop a mobile app, integrating online ingredient ordering (E-commerce).

II. Requirements

1. Theory and Practice:

• Master the Agile/Scrum software development process.
• Understand the principles of energy calculation (Calories/Macros) in food.
• Knowledge of user data security and optimization of large database queries.

2. Implementation and Development:

• Backend: Build a standard RESTful API with Laravel Framework 12; Manage MySQL database.
• Frontend: Use Blade Templates, TailwindCSS/Bootstrap to build a user-friendly and visually appealing interface (Food Stylist).
• AI/Algorithm: Successfully build an algorithm for searching ingredients and integrate a Chatbot API to support cooking.

3. Experimentation:

• Functional testing (Unit Test, Feature Test) to ensure business processes operate correctly.
• Evaluate system performance under high traffic (Load Testing).
• Run tests with real-world data (50-100 standard recipes) to verify the accuracy of the recommendation algorithm.

III. References

1.	https://laravel.com/docs/12.x, Laravel 12.x Documentation, Laravel, 2026.
3.	https://spoonacular.com/food-api, Spoonacular Food & Recipe API (Tham khảo cấu trúc dữ liệu dinh dưỡng), 2026.
4.	https://ai.google.dev/gemini-api/docs, Gemini API Documentation, Google AI Studio, 2026.
5.	https://tailwindcss.com/docs, Tailwind CSS Documentation, 2026.
6.	https://dev.mysql.com/doc/, MySQL Reference Manual, Oracle, 2026.
