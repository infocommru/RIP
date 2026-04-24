import asyncio
from pyppeteer import launch
import sys
import os


async def generate_pdf(url, pdf_path):
    os.environ["PUPPETEER_SKIP_CHROMIUM_DOWNLOAD"] = "true"

    browser = await launch(executablePath='/usr/bin/chromium', headless=True, 
        userDataDir="/tmp/pyppeteer_profile",
        args=['--no-sandbox', '--disable-dev-shm-usage', '--disable-setuid-sandbox', '--disable-gpu']) 
    page = await browser.newPage()

    await page.goto(url)

    await page.pdf({'path': pdf_path, 'format': 'A5'})

    await browser.close()


# Run the function

path = sys.argv[1]
path = path.replace('/web/print/forma-pdf','/web/print/forma')
url = "http://127.0.0.1"+path
#print(url)
asyncio.get_event_loop().run_until_complete(generate_pdf(url, './temp/pdf_form.pdf'))
 
