--Magig SQL statement, to collect all data (Redundant data in API-transfer, because of multiple cap data)

SELECT cap.* , shockcategory.shockcategoryText
FROM ((cap INNER JOIN screference ON cap.capID = screference.capID) INNER JOIN shockcategory ON screference.shockcategoryID = shockcategory.shockcategoryid)