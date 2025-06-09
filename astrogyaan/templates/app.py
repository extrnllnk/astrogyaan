from flask import Flask, render_template, request
import requests
from datetime import datetime

app = Flask(__name__)

@app.route('/', methods=['GET', 'POST'])
def index():
    # Default values
    default_lat = 28.6139
    default_lng = 77.2090
    lat = default_lat
    lng = default_lng
    date = datetime.today().strftime('%Y-%m-%d')
    sunrise_data = None
    error = None

    if request.method == 'POST':
        # Get values from form input
        try:
            lat = float(request.form.get('lat', lat))
            lng = float(request.form.get('lng', lng))
        except ValueError:
            error = "Invalid coordinates. Please enter valid numbers."
            lat = default_lat
            lng = default_lng

        # Validate date format
        user_date = request.form.get('date', date)
        try:
            datetime.strptime(user_date, '%Y-%m-%d')
            date = user_date
        except ValueError:
            error = "Invalid date format. Please use YYYY-MM-DD."

    # Call the Sunrise-Sunset API
    api_url = f"https://api.sunrise-sunset.org/json?lat={lat}&lng={lng}&date={date}"
    response = requests.get(api_url)
    if response.status_code == 200:
        data = response.json()
        if data['status'] == 'OK':
            sunrise_data = data['results']
        else:
            error = "API returned error status."
    else:
        error = f"Failed to fetch data. Status code: {response.status_code}"

    return render_template('index.html',
                           sunrise_data=sunrise_data,
                           error=error,
                           lat=lat,
                           lng=lng,
                           date=date)

if __name__ == '__main__':
    app.run(debug=True)
