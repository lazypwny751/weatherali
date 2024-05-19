#!/usr/bin/env python3

import	os
import	json
import	sqlite3
import	argparse
import	pandas as pd
from 	sklearn.model_selection	import train_test_split
from 	sklearn.linear_model	import LinearRegression

class WeatherAli:
	def __init__(self, dosya, database="weatherali"):
		self.database = (f"{database}.db")
		self.dosya = dosya
		self.df = pd.read_excel(self.dosya)
		self.df.fillna(0, inplace=True)

	def egit(self):
		x = self.df[
			[
				"ışınım",
				"sıcaklık",
				"nem",
				"rüzgar"
			]
		]

		y = self.df["hava_durumu"]

		x_train, x_test, y_train, y_test = train_test_split(x, y, test_size=0.2, random_state=42)

		model = LinearRegression()
		model.fit(
			x_train, 
			y_train
		)

		train_score = model.score(x_train, y_train)
		test_score = model.score(x_test, y_test)
		print("Eğitim kümesi doğruluğu:", train_score)
		print("Test kümesi doğruluğu:", test_score)

		predictions = model.predict(x_test)
		data = []

		for e in predictions:
			data += [ int(e) ]
	
		return data

	def jsonFormat(data, breakpoint=365):
		counter = 0
		print("{")
		for d in data:
			counter += 1
			print(f"\t{counter}: \"{d}\"")
			if counter == breakpoint:
				break
		print("}")

parser = argparse.ArgumentParser(description="Yapay zeka hava durumu tahmini programı.")
parser.add_argument(
	"-d",
	"--dosya",
	help="Eğitim verisi (xlsx)."
)
parser.add_argument(
	"-y",
	"--yaz",
	help="Veriyi, veritabanı dosyasına dosyaya yaz"
)

if __name__ == "__main__":
	args = parser.parse_args()

	if args.dosya and os.path.exists(args.dosya):
		max = 365
		dosya = args.dosya
		wa = WeatherAli(dosya)
		data = wa.egit()
		WeatherAli.jsonFormat(data, max)
		if args.yaz:
			conn = None
			try:
				conn = sqlite3.connect(args.yaz)
			except sqlite3.Error as e:
				print(e)

			c = conn.cursor()
			c.execute("DROP TABLE IF EXISTS tahminiHavaDurumu")
			c.execute("CREATE TABLE IF NOT EXISTS tahminiHavaDurumu (id INTEGER PRIMARY KEY, value TEXT)")

			counter = 0
			for e in data:
				counter += 1
				c.execute("INSERT INTO tahminiHavaDurumu (value) VALUES (?)", (e,))
				if counter == max:
					break

			conn.commit()
			conn.close()
	else:
		print("HATA: Lütfen \"--dosya\" argümanına geçerli bir \"xlsx (Excel)\" dosyası belirtin.")
		exit(False)