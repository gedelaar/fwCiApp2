<table cellpadding="0" cellspacing="0">
    <thead>
    <th>
            <td>PRODUCT ID</td>
            <td>PRODUCT NAME</td>
            <td>CATEGORY</td>
            <td>PRICE</td>
    </th>
    </thead>
 
    <tbody>
            <?php foreach($csvData as $field){
				//print_r $csvData;
			?>
                <tr>
                    <td><?=$field['Poule']?></td>
                   <td><?=$field['Code']?></td>
                    <td><?=$field['Naam01']?></td>
                    <td><?=$field['Naam02']?></td>					
                    <td><?=$field['Naam03']?></td>					
                    <td><?=$field['Naam04']?></td>					
                    <td><?=$field['Naam05']?></td>					
                    <td><?=$field['Naam06']?></td>					
                    <td><?=$field['Naam07']?></td>					
                    <td><?=$field['Naam08']?></td>
					<td><?=$field['Official 01']?></td>
					<td><?=$field['Official 02']?></td>
					<td><?=$field['Official 03']?></td>
					<td><?=$field['Official 04']?></td>
					<td><?=$field['Official 05']?></td>
					<td><?=$field['Official 06']?></td>
					<td><?=$field['Official 07']?></td>
					<td><?=$field['Official 08']?></td>
					<td><?=$field['Veld']?></td>					
					<td><?=$field['Org Zn01']?></td>					
					<td><?=$field['Org Zn02']?></td>					
					<td><?=$field['Org Zn03']?></td>					
					<td><?=$field['Org Zn04']?></td>					
					<td><?=$field['Org Zn05']?></td>					
					<td><?=$field['Org Zn06']?></td>					
					<td><?=$field['Org Zn07']?></td>					
                </tr>
            <?php }?>
    </tbody>
 
</table>