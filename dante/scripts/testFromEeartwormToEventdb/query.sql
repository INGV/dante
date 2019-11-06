SELECT 
    p.id AS phase__id, 
    p.weight_out AS phase__weight_out, 
    p.isc_code AS phase__weight_out, 
    k.id AS pick__id, 
    k.weight AS pick__weight, 
    h.ot AS hypocenter__ot, 
    h.fk_type_hypocenter, 
    h.quality AS hypocenter__quality, 
    h.region AS hypocenter__region, 
    m.mag, 
    m.mag_quality,
    te.name
FROM pick k 
LEFT  JOIN phase p ON p.fk_pick=k.id 
LEFT  JOIN hypocenter h ON p.fk_hypocenter=h.id 
LEFT  JOIN magnitude m ON m.fk_hypocenter=h.id 
LEFT  JOIN event e ON h.fk_event=e.id
LEFT  JOIN type_event te ON e.fk_type_event=te.id
WHERE k.modified >= "2017-06-01 00:00:00" 
ORDER BY k.id DESC 
LIMIT 80;
