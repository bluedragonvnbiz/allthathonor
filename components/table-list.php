<div class="card">
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-hover mb-0">
			  <thead>
			    <tr>
			      <th>위치</th>
			      <th>섹션</th>
			      <th>상단 문구</th> 
			      <th>메인 타이틀</th>
			      <th>노출상태</th>
			      <th>최종수정일</th>
			    </tr>
			  </thead>
			  <tbody>
			  	<?php  

			  	for ($i = 3; $i >= 0; $i--) { ?>				  	
				   <tr>				      
				      <td><a href="#">메인</a></td>
				      <td><a href="#"><?= $i ?></a></td>
				      <td><a href="#">premium service, tailored for you</a></td>
				      <td><a href="#">All THAT HONORS CLUB</a></td>
				      <td><a href="#">노출</a></td>
				      <td><a href="#">2024-01-01</a></td>
				   </tr>
			  	<?php
			  	}
			  	?>

			  </tbody>
			</table>
		</div>
	</div>
</div>