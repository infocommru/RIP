import pandas as pd
import sys

filepath = sys.argv[1]
filepath_out = "./temp/out.csv"
df = pd.read_excel(filepath)
df.to_csv(filepath_out, index=False, header=True)