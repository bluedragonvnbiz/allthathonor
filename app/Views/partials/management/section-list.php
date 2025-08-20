<?php
/**
 * Table List Partial
 * Display data in table format
 */
?>

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
			  	<?php if (!empty($sections)): ?>
			  		<?php
					// Helper function to safely get string values
					$getValue = function($data, $key, $default = '-') {
						$value = $data[$key] ?? $data['section_info'][$key] ?? $data['content_info'][$key] ?? $default;
						
						// Handle special cases for complex fields
						if ($key === 'content_main_title' && is_array($value)) {
							// For section 3: combine part1 and part2
							$part1 = $value['part1'] ?? '';
							$part2 = $value['part2'] ?? '';
							return trim($part1 . ' ' . $part2);
						}
						
						return is_array($value) ? $default : (string)$value;
					};
			  		
			  		// Collect all section data with section number for sorting
			  		$sectionDataList = [];
			  		foreach ($sections as $section) {
			  			try {
			  				$sectionService = new \App\Services\SectionService();
			  				// Use the directory from section data to load correct page data
			  				$page = $section['directory'] ?? 'main';
			  				$sectionData = $sectionService->getSectionData($section['key'], $page);
			  				
			  				$sectionNumber = $getValue($sectionData, 'section_section_number', '999'); // Default to end if no number
			  				
			  				$sectionDataList[] = [
			  					'key' => $section['key'],
			  					'data' => $sectionData,
			  					'section_number' => (int)$sectionNumber,
			  					'directory' => $page
			  				];
			  			} catch (\Exception $e) {
			  				// Skip sections that can't be loaded
			  				continue;
			  			}
			  		}
			  		
			  		// Sort by page first, then by section number
			  		usort($sectionDataList, function($a, $b) {
			  			// First compare by directory/page
			  			$pageA = $a['directory'] ?? 'main';
			  			$pageB = $b['directory'] ?? 'main';
			  			
			  			if ($pageA !== $pageB) {
			  				// Use alphabetical order for pages
			  				return strcmp($pageA, $pageB);
			  			}
			  			
			  			// If same page, sort by section number
			  			return $a['section_number'] - $b['section_number'];
			  		});
			  		
			  		foreach ($sectionDataList as $item):
			  			$sectionKey = $item['key'];
			  			$sectionData = $item['data'];
			  			
			  			$location = $getValue($sectionData, 'section_location', '메인');
			  			$sectionNumber = $getValue($sectionData, 'section_section_number', '1');
			  			$topPhrase = $getValue($sectionData, 'content_top_phrase', '-');
			  			$mainTitle = $getValue($sectionData, 'content_main_title', '-');
			  			$exposureStatus = $getValue($sectionData, 'section_exposure_status', 'expose');
			  			
			  			// Get last modified date from database
			  			$page = $item['directory'] ?? 'main';
			  			$lastModified = $sectionService->getSectionLastModified($sectionKey, $page);
			  			$lastModified = $lastModified ? date('Y-m-d', strtotime($lastModified)) : '-';
			  		?>
			  		<tr>				      
			  			<td><a href="/management/edit/?section=<?= $sectionKey ?>&section_page=<?= $page ?>"><?= htmlspecialchars($location) ?></a></td>
			  			<td><a href="/management/edit/?section=<?= $sectionKey ?>&section_page=<?= $page ?>"><?= htmlspecialchars($sectionNumber) ?></a></td>
			  			<td><a href="/management/edit/?section=<?= $sectionKey ?>&section_page=<?= $page ?>"><?= htmlspecialchars($topPhrase) ?></a></td>
			  			<td><a href="/management/edit/?section=<?= $sectionKey ?>&section_page=<?= $page ?>"><?= htmlspecialchars($mainTitle) ?></a></td>
			  			<td><a href="/management/edit/?section=<?= $sectionKey ?>&section_page=<?= $page ?>"><?= htmlspecialchars($exposureStatus === 'expose' ? '노출' : '미노출') ?></a></td>
			  			<td><a href="/management/edit/?section=<?= $sectionKey ?>&section_page=<?= $page ?>"><?= htmlspecialchars($lastModified) ?></a></td>
			  		</tr>
			  		<?php endforeach; ?>
			  	<?php else: ?>
			  		<tr>
			  			<td colspan="6" class="text-center">사용 가능한 섹션이 없습니다.</td>
			  		</tr>
			  	<?php endif; ?>
			  </tbody>
			</table>
		</div>
	</div>
</div>
