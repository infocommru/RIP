from pdf2image import convert_from_path

pages = convert_from_path('./temp/pdf_form.pdf')

# Save each page as a JPEG file using Pillow

for i, page in enumerate(pages):
	page.save(f'./temp/pdf.jpg', 'JPEG')
	break