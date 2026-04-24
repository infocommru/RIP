import os
import sys
import glob
import pandas as pd

if __name__ == "__main__":
    dirname = sys.argv[1]
    print(dirname)

    os.chdir("/var/www/html/web/upload/part")
    files = glob.glob(dirname + "/**/*.xlsx", recursive=True)

    for index, file in enumerate(files):
        filepath_out = dirname + "/" + str(index) + ".csv"
        df = pd.read_excel(file)
        df.to_csv(filepath_out, index=False, header=True)
        f = open(dirname + "/" + "info.txt", 'a')
        f.write(str(index) + "=>" + file + "\n")
        f.close()

