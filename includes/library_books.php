<?php
require_once('com_shortcode.php');
/**
 * Created by PhpStorm.
 * User: stephen
 */

class library_shortcode extends com_shortcode {

	const name          = 'library'; // the text entered by the user (inside square brackets)
	const section_name  = 'library_settings'; //unique section id that organizes each setting
	const section_title = 'Library [library]'; // Section header for this shortcode's settings
	const tinymce_library_resource   = 'resource'; // if unset, show first resources, with a dropdown to select other resources. otherwise, limit to the specified resource

	const resource_ebooks = 'E-Books';
	const resource_edatabases = 'E-Databases';
	const resource_ejournals = 'E-Journals';
	const resource_etextbooks = 'E-Textbooks';


	public function get_name() {
		return self::name;
	}

	public function get_css() {
		return '';
	}

	public function get_section_name() {
		return self::section_name;
	}

	public function get_section_title() {
		return self::section_title;
	}

	public function add_settings() {
		$this->add_setting_custom_fields_group();
		
	}
	

	public function replacement( $attrs = null ) {
		$attrs = shortcode_atts(
			array(
				self::tinymce_library_resource => '', //default to show first resource, with a dropdown to select others
			), $attrs, self::name
		);
		if ($attrs[ self::tinymce_library_resource ]) {
			// shortcode category is defined. show only that category.
			$library_resource = $attrs[ self::tinymce_library_resource ];
		}

		ob_start(); // create a new buffer
?>

		<h2>Library Search</h2>
		<form id="library-search" class="loading" >
		<?php
		if (!$library_resource) {
			?>
			<select id="resource" >
				<option>Loading Resources</option>
			</select>
			<?php
		} else {
			?>
			<input type="hidden" id="resource" value="<?php echo $library_resource ?>" />
			<?php
		}
		if (($library_resource == self::resource_etextbooks) || (!$library_resource)){
			// only e-textbooks use sessions
			?>
			<select id="session">
				<option>Loading Sessions</option>
			</select>
			<?php
		}
		
		?>
			<select id="subject">
				<option>Loading Subjects</option>
			</select>
			<input id="library-search-text" type="text" placeholder="Title, Author, or Provider" />
			<input type="submit" id="library-search-text-submit" value="Search" />
			<!--<label for="show-advanced-options">Show advanced Options</label>
			<input type="checkbox" id="show-advanced-options">
			<div class="advanced-search-options">
				<label for="title">Title</label>
				<input type="text" id="title" placeholder="Gray's Anatomy, 40e" />
		
				<label for="author">Author</label>
				<input type="text" id="author" placeholder="Susan Standring" />
		
				<label for="isbn-or-issn">ISBN or ISSN</label>
				<input type="number" id="isbn-or-issn" placeholder="" />
				<input type="text" id="provider" />
				<div class="year-search">
					<input type="radio" name="year-range" value="specific" checked="checked">Exact Year
					<input type="radio" name="year-range" value="range">Year Range
					<input class="year-specific" type="text" id="year" />
					<input class="year-range" type="number" min="1900" max="2099" step="1" id="year-start" />
					<input class="year-range" type="text" id="year-end" />
				</div>
			</div>-->

			<label for="sortby">Sort By</label>
			<select autocomplete="off" id="sortby">
				<option selected="selected" value="Title">Title</option>
				<option value="Authors">Author</option>
				<?php
					if ($library_resource != self::resource_ejournals){
						// don't allow sortable for date range for e-journals.
						?>
						<option value="Year">Year</option>
						<?php
					}
				?>
				<option value="Provider">Provider</option>
			</select>
			<select autocomplete="off" id="sortby-order">
				<option selected="selected" value="ascending">A - Z</option>
				<option value="descending">Z - A</option>
			</select>
			<ul id="search-pagination-top" class="pagination-sm"></ul>
			<div id="search-pagination-range-top" class="search-pagination-range"><span></span></div>
			<div id="search-pagination-alternate-top" class="pagination-alternate-input"></div>

			<div class="loader">Loading...</div>

			<div id="search-results"></div>

			<ul id="search-pagination-bottom" class="pagination-sm"></ul>
			<div id="search-pagination-alternate-bottom" class="pagination-alternate-bottom"></div>
			<div id="restricted-explanation" style="display: none;"><span class="restricted">&nbsp;</span><span>Off-campus access limited to College of Medicine users. Access for non-College of Medicine users available onsite at the College of Medicine campus.</span></div>
			<br />
			<div id="search-pagination-range-bottom" class="search-pagination-range"><span></span></div>

		</form>

<?php
		$output = ob_get_clean(); // stop the buffer and get the contents that would have been echoed out.
		return $output;
	}

} 