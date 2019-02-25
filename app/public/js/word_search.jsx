class WordSearch extends React.Component {

  /**
   * Constructor - sets the initial state and passes props through.
   */
  constructor(props) {
    super(props);
    this.state = {
      error_status: null,
      match_type: 'begins_with',
      timeoutID: undefined,
      word_search: '',
      words: []
    };
  }

  /**
   * Perform a http request to get the data.
   *
   * TODO - this needs to have the domain name be inserted dynamically
   */
  fetchWords() {
    if (this.state.timeoutID !== null) {
      clearTimeout(this.state.timeoutID);
      this.setState({
         timeoutID: null
      });
    }

    var uri = '//local.api.basereality.com/word_search?search_string=' + encodeURI(this.state.word_search) + '&match_type=' + encodeURI(this.state.match_type);

    fetch(uri)
      .then((response) => {
          if (!response.ok) {
            this.setState({error_status: response.statusText});
            throw Error(response.statusText);
          }
          return response;
        }
      )
      .then((response) => response.json())
      .then(
        (words) => this.setState({words: words})
      )
      .catch();
  }

  /**
   * Handle the search type being changed.
   *
   */
  handleMatchChange(event) {
    this.setState({
      match_type: event.target.value,
    });
    setTimeout(() => this.fetchWords(), 200);
  }

  /**
   * Handle letters being typed in the search field.
   *
   * It stores the letters and schedules a fetch of words in 200ms
   *
   */
  handleSearchChange(event) {
    if (this.state.timeoutID !== undefined) {
      clearTimeout(this.state.timeoutID);
    }

    let newTimeoutID = setTimeout(() => this.fetchWords(), 200);

    this.setState({
      word_search: event.target.value,
      timeoutID: newTimeoutID
    });
  }

  /**
   * When the user clicks away from the input, trigger the fetch
   * straight away.
   */
  handleSearchBlur() {
    this.fetchWords();
  }

  /**
   * Render the error message if there is one.
   */
  renderErrorDisplay() {
    if (this.state.error_status === null) {
      return (<div></div>);
    }
    return (<div className="error">{this.state.error_status}</div>);
  }

  /**
   * Renders the whole component.
   */
  render() {

    var result_display = '';

    if (this.state.word_search === '') {
      result_display = (<div>Start typing to match letters to words </div>);
    }
    else if (this.state.words.length === 0) {
      result_display = (<div>No words found.</div>);
    }
    else {
      const listItems = this.state.words.map((word) => (<li><span>{word}</span></li>));

      result_display =  (<ul>{listItems}</ul>);
    }

    const errorDisplay = (this.renderErrorDisplay());

    return (
      <div>
        <div className='react_input'>
          <input type="text"
                 value={this.state.word_search}
                 placeholder="Enter some letters to search"
                 onChange={(e) => this.handleSearchChange(e)}
                 onBlur={(e) => this.handleSearchBlur(e)}
                 size={50}
          />


          <select value={this.state.match_type} onChange={(e) => this.handleMatchChange(e)}>
            <option value="begins_with">Begins with</option>
            <option value="end_with">End with</option>
          </select>
        </div>

        <div>
          {result_display}
        </div>

        {errorDisplay}
      </div>
    );
  }
}


/**
 * Add the word search to an element that has an ID word_search
 */
ReactDOM.render(
    <WordSearch />,
    document.getElementById('word_search')
);
