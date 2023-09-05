import React, { Component } from 'react';
import {
  Card, Stack, ResourceList, TextStyle, Checkbox
} from '@shopify/polaris';

class Header extends Component {
  render() {
    const {
      homeTeamFirst, between_players
    } = this.props;

    return (
      <Card>
        <ResourceList.Item>
          <Stack>
            <Stack.Item>
              <div style={{ width: '40px' }}>
                <Checkbox
                  checked={this.props.checked}
                  disabled={this.props.isCheckDisabled}
                  onChange={this.props.onToggleSelectAll}
                />
              </div>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '60px' }}>ID</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '60px' }}>Ref id</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '160px' }}>Start date</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '240px' }}>{between_players ? 'Player one' : (homeTeamFirst ? 'Home team' : 'Away team')}</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '80px' }} />
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '240px' }}>{between_players ? 'Player Two' : (homeTeamFirst ? 'Away team' : 'Home team')}</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item fill>
              <TextStyle variation="strong">
                <div style={{ width: '100px' }}>Status</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '190px' }}>Action</div>
              </TextStyle>
            </Stack.Item>
          </Stack>
        </ResourceList.Item>
      </Card>
    );
  }
}

Header.defaultProps = {
  homeTeamFirst: false
};

export default Header;
