import asyncio
import pyppeteer
import sys
import os

async def generate_pdf(url, pdf_path):
    os.environ["PUPPETEER_SKIP_CHROMIUM_DOWNLOAD"] = "true"

    browser = await pyppeteer.connect(browserWSEndpoint='ws://chromium:3000')
    page = await browser.newPage()

    await page.goto(url)
    pdf = await page.pdf({'format': 'A5',})

    with open(pdf_path, 'wb') as f:
        f.write(pdf)

    await browser.disconnect()


# Run the function

path = sys.argv[1]
path = path.replace('/web/print/forma-pdf','/web/print/forma')
url = "http://rip"+path
#print(url)
asyncio.get_event_loop().run_until_complete(generate_pdf(url, './temp/pdf_form.pdf'))
 
