## Get Started

First, Clone the repo to your server

```
https://github.com/arifhossen-dev/fun-GPT.git
```
Next, Install composer
```
composer install
```
Next, prepare the environment file
```
cp .env.example .env
```
Next, Generate App key
```
php artisan key:generate
```

Next, collect and assign the OpenAI API key and Organization key `.env` file
```
OPENAI_API_KEY=sk-...
OPENAI_ORGANIZATION=org-...
```
Now open the terminal and run the following command
```
php artisan chat
```
Next follow the instructions in prompts

Then ask your questions and get response as reply from OpenAI as long as you need.

Enjoy.
