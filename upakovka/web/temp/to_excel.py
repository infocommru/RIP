# -*- coding: utf-8 -*-

import os
import glob
import csv
from xlsxwriter.workbook import Workbook

#print(123);exit;

workbook = Workbook('./temp/search.xlsx')
worksheet = workbook.add_worksheet()
with open('./temp/search.csv', 'rt', encoding='utf8') as f:
    reader = csv.reader(f,delimiter=';')
    for r, row in enumerate(reader):
        for c, col in enumerate(row):
            worksheet.write(r, c, col)
workbook.close()