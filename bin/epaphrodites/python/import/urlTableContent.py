import sys
import json
import requests
from bs4 import BeautifulSoup
sys.path.append('bin/epaphrodites/python/config/')
from initJsonLoader import InitJsonLoader

class UrlTableContent:
    
    def __init__(self, url):
        
        self.url = url

    def get_webpage_content(self):
        
        response = requests.get(self.url)
        
        self.html_content = response.text

    def extract_table_data(self):
        
        soup = BeautifulSoup(self.html_content, 'html.parser')
        
        table = soup.find('table')

        self.table_data = []
        
        for row in table.find_all('tr'):
            
            row_data = []
            
            cells = row.find_all(['th', 'td'])
            
            for cell in cells:
                
                row_data.append(cell.text)
                
            self.table_data.append(row_data)

    def scrape_and_return_data(self):
        
        self.get_webpage_content()
        
        self.extract_table_data()
        
        return self.table_data

if __name__ == "__main__":
    
    json_values = sys.argv[1]
    
    url_table_content = UrlTableContent('')
    
    values = InitJsonLoader.loadJsonValues(json_values)
    
    url = values.get('url')
    
    scraper = UrlTableContent(url)
    
    table_data = scraper.scrape_and_return_data()
    
    print(json.dumps(table_data))
